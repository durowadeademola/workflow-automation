/**
 * AI Chat Widget — Embeddable Script
 * -----------------------------------
 * Drop this script at the bottom of any HTML page.
 * Configure via window.ChatWidgetConfig before the script loads.
 *
 * USAGE:
 * <script>
 *   window.ChatWidgetConfig = {
 *     webhookUrl:   'https://your-n8n.com/webhook/chat-widget',
 *     businessName: 'Your Business Name',
 *     agentName:    'AI Assistant',         // optional
 *     primaryColor: '#0f6e56',              // optional
 *     waNumber:     '2348012345678',        // WhatsApp number (no + sign)
 *     greeting:     'Hi! How can I help?',  // optional
 *     systemPrompt: 'You are a helpful assistant for ...',
 *     position:     'right',               // 'right' or 'left', default right
 *     quickReplies: ['Book appointment', 'Our services', 'Talk to a human'], // optional
 *   };
 * </script>
 * <script src="https://your-cdn.com/chat-widget.js"></script>
 */

(function () {
  'use strict';

  const cfg = Object.assign({
    webhookUrl:   '',
    businessName: 'AI Assistant',
    agentName:    'AI Assistant',
    primaryColor: '#0f6e56',
    waNumber:     '',
    greeting:     "👋 Hello! How can I help you today?",
    systemPrompt: 'You are a helpful AI assistant. Be friendly and concise.',
    position:     'right',
    quickReplies: [],
  }, window.ChatWidgetConfig || {});

  /* ── Helpers ── */
  const $ = id => document.getElementById(id);
  const lighter = (hex, amt) => {
    const n = parseInt(hex.replace('#',''), 16);
    const r = Math.min(255, (n >> 16) + amt);
    const g = Math.min(255, ((n >> 8) & 0xff) + amt);
    const b = Math.min(255, (n & 0xff) + amt);
    return '#' + [r,g,b].map(x => x.toString(16).padStart(2,'0')).join('');
  };
  const darken = hex => lighter(hex, -30);
  const now = () => new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

  /* ── Inject styles ── */
  const style = document.createElement('style');
  style.textContent = `
    #cw-root *, #cw-root *::before, #cw-root *::after { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    #cw-root { position: fixed; ${cfg.position}: 20px; bottom: 20px; z-index: 999999; }

    #cw-bubble {
      width: 56px; height: 56px; border-radius: 50%;
      background: ${cfg.primaryColor}; border: none; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 4px 20px rgba(0,0,0,0.25);
      transition: transform .2s, box-shadow .2s;
    }
    #cw-bubble:hover { transform: scale(1.08); box-shadow: 0 6px 28px rgba(0,0,0,0.3); }
    #cw-bubble svg { width: 26px; height: 26px; }
    #cw-dot {
      position: absolute; top: 2px; ${cfg.position}: 2px;
      width: 13px; height: 13px; border-radius: 50%;
      background: #25d366; border: 2px solid white;
    }

    #cw-window {
      position: absolute; bottom: 68px; ${cfg.position}: 0;
      width: 330px; height: 480px;
      background: #fff; border-radius: 16px;
      box-shadow: 0 8px 40px rgba(0,0,0,0.2);
      display: flex; flex-direction: column; overflow: hidden;
      transform: scale(0.85) translateY(16px); opacity: 0; pointer-events: none;
      transition: transform .25s cubic-bezier(.34,1.56,.64,1), opacity .2s;
    }
    #cw-window.cw-open { transform: scale(1) translateY(0); opacity: 1; pointer-events: all; }

    .cw-header {
      background: ${cfg.primaryColor}; padding: 13px 14px;
      display: flex; align-items: center; gap: 10px; flex-shrink: 0;
    }
    .cw-avatar {
      width: 36px; height: 36px; border-radius: 50%;
      background: ${lighter(cfg.primaryColor, 60)};
      display: flex; align-items: center; justify-content: center;
      font-size: 13px; font-weight: 700; color: white; flex-shrink: 0;
    }
    .cw-agent-name { font-size: 14px; font-weight: 600; color: white; line-height: 1.2; }
    .cw-status { font-size: 11px; color: rgba(255,255,255,.75); display: flex; align-items: center; gap: 4px; }
    .cw-sdot { width: 6px; height: 6px; background: #5dcaa5; border-radius: 50%; }
    .cw-close {
      position: absolute; top: 10px; right: 12px;
      background: none; border: none; cursor: pointer;
      color: rgba(255,255,255,.8); font-size: 20px; line-height: 1; padding: 2px 6px;
      border-radius: 4px;
    }
    .cw-close:hover { color: white; }

    #cw-messages {
      flex: 1; overflow-y: auto; padding: 12px 10px;
      display: flex; flex-direction: column; gap: 9px;
      background: #f5f7f6;
    }
    #cw-messages::-webkit-scrollbar { width: 4px; }
    #cw-messages::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }

    .cw-msg { display: flex; flex-direction: column; max-width: 84%; }
    .cw-msg.cw-bot { align-self: flex-start; }
    .cw-msg.cw-user { align-self: flex-end; }
    .cw-bubble-msg {
      padding: 9px 13px; border-radius: 14px;
      font-size: 13.5px; line-height: 1.5; word-break: break-word;
      white-space: pre-wrap;
    }
    .cw-bot .cw-bubble-msg {
      background: white; color: #1a1a1a;
      border-bottom-left-radius: 4px;
      border: 0.5px solid #e2e2e2;
    }
    .cw-user .cw-bubble-msg {
      background: ${cfg.primaryColor}; color: white;
      border-bottom-right-radius: 4px;
    }
    .cw-time { font-size: 10px; color: #bbb; margin-top: 3px; padding: 0 3px; }
    .cw-user .cw-time { align-self: flex-end; }

    .cw-typing { display: flex; gap: 4px; align-items: center; padding: 10px 13px; background: white; border-radius: 14px; border-bottom-left-radius: 4px; border: 0.5px solid #e2e2e2; width: fit-content; }
    .cw-typing span { width: 7px; height: 7px; background: #ccc; border-radius: 50%; animation: cwBounce 1.2s infinite; }
    .cw-typing span:nth-child(2) { animation-delay: .2s; }
    .cw-typing span:nth-child(3) { animation-delay: .4s; }
    @keyframes cwBounce { 0%,60%,100% { transform: translateY(0); } 30% { transform: translateY(-5px); } }

    .cw-wa-btn {
      display: inline-flex; align-items: center; gap: 7px;
      background: #25d366; color: white; border: none;
      border-radius: 10px; padding: 8px 13px;
      font-size: 12px; font-weight: 600; cursor: pointer;
      margin-top: 6px; text-decoration: none;
      transition: background .15s;
    }
    .cw-wa-btn:hover { background: #1fba57; }
    .cw-wa-btn svg { width: 15px; height: 15px; flex-shrink: 0; }

    #cw-qr {
      display: flex; flex-wrap: wrap; gap: 6px;
      padding: 4px 10px 8px; background: #f5f7f6;
    }
    .cw-qr-btn {
      background: white; border: 1px solid ${cfg.primaryColor};
      color: ${cfg.primaryColor}; border-radius: 20px;
      padding: 5px 12px; font-size: 12px; cursor: pointer;
      white-space: nowrap; transition: background .15s, color .15s;
    }
    .cw-qr-btn:hover { background: ${cfg.primaryColor}; color: white; }

    #cw-input-row {
      display: flex; align-items: center; gap: 8px;
      padding: 9px 10px; border-top: 0.5px solid #e8e8e8;
      background: white; flex-shrink: 0;
    }
    #cw-input {
      flex: 1; border: 0.5px solid #ddd; border-radius: 20px;
      padding: 8px 14px; font-size: 13px; outline: none;
      background: #f5f7f6; color: #1a1a1a;
    }
    #cw-input:focus { border-color: ${cfg.primaryColor}; background: white; }
    #cw-input::placeholder { color: #bbb; }
    #cw-send {
      width: 34px; height: 34px; border-radius: 50%;
      background: ${cfg.primaryColor}; border: none; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0; transition: background .15s;
    }
    #cw-send:hover { background: ${darken(cfg.primaryColor)}; }
    #cw-send svg { width: 15px; height: 15px; }
    #cw-footer { font-size: 10px; color: #bbb; text-align: center; padding: 4px 0 6px; background: white; flex-shrink: 0; }
  `;
  document.head.appendChild(style);

  /* ── HTML ── */
  const root = document.createElement('div');
  root.id = 'cw-root';
  root.innerHTML = `
    <div id="cw-window">
      <div class="cw-header">
        <div class="cw-avatar">${cfg.agentName.charAt(0).toUpperCase()}</div>
        <div>
          <div class="cw-agent-name">${cfg.agentName}</div>
          <div class="cw-status"><span class="cw-sdot"></span> Online</div>
        </div>
        <button class="cw-close" id="cw-close-btn">&#215;</button>
      </div>
      <div id="cw-messages"></div>
      <div id="cw-qr"></div>
      <div id="cw-input-row">
        <input type="text" id="cw-input" placeholder="Type a message..." autocomplete="off" />
        <button id="cw-send" aria-label="Send">
          <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="22" y1="2" x2="11" y2="13"/>
            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
          </svg>
        </button>
      </div>
      <div id="cw-footer">Powered by AI · <a href="https://wa.me/${cfg.waNumber}" target="_blank" style="color:inherit;">WhatsApp us</a></div>
    </div>
    <button id="cw-bubble" aria-label="Open chat">
      <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      </svg>
    </button>
    <div id="cw-dot"></div>
  `;
  document.body.appendChild(root);

  /* ── State ── */
  let isOpen = false;
  let isBusy = false;
  let history = [];

  /* ── Toggle ── */
  function toggle() {
    isOpen = !isOpen;
    $('cw-window').classList.toggle('cw-open', isOpen);
    if (isOpen && $('cw-messages').children.length === 0) {
      setTimeout(greet, 320);
    }
    if (isOpen) setTimeout(() => $('cw-input').focus(), 350);
  }

  function greet() {
    appendMsg('bot', cfg.greeting);
    if (cfg.quickReplies.length) renderQR(cfg.quickReplies);
  }

  function renderQR(items) {
    const qr = $('cw-qr');
    qr.innerHTML = '';
    items.forEach(label => {
      const btn = document.createElement('button');
      btn.className = 'cw-qr-btn';
      btn.textContent = label;
      btn.onclick = () => { qr.innerHTML = ''; send(label); };
      qr.appendChild(btn);
    });
  }

  /* ── Append message ── */
  function appendMsg(role, text) {
    const msgs = $('cw-messages');
    const wrap = document.createElement('div');
    wrap.className = `cw-msg cw-${role}`;

    const bubble = document.createElement('div');
    bubble.className = 'cw-bubble-msg';
    bubble.textContent = text;
    wrap.appendChild(bubble);

    const needsWA = role === 'bot' && cfg.waNumber && (
      /whatsapp|agent|human|speak to|talk to|contact us/i.test(text)
    );
    if (needsWA) {
      const encoded = encodeURIComponent(`Hello, I need assistance with ${cfg.businessName}`);
      const a = document.createElement('a');
      a.className = 'cw-wa-btn';
      a.href = `https://wa.me/${cfg.waNumber}?text=${encoded}`;
      a.target = '_blank';
      a.innerHTML = `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.99 2C6.477 2 2 6.477 2 11.99c0 1.762.459 3.413 1.262 4.848L2 22l5.338-1.22A9.951 9.951 0 0011.99 22C17.523 22 22 17.523 22 11.99 22 6.477 17.523 2 11.99 2z"/></svg>Chat on WhatsApp`;
      wrap.appendChild(a);
    }

    const time = document.createElement('div');
    time.className = 'cw-time';
    time.textContent = now();
    wrap.appendChild(time);

    msgs.appendChild(wrap);
    msgs.scrollTop = msgs.scrollHeight;
    history.push({ role: role === 'bot' ? 'assistant' : 'user', content: text });
  }

  function showTyping() {
    const msgs = $('cw-messages');
    const wrap = document.createElement('div');
    wrap.className = 'cw-msg cw-bot';
    wrap.id = 'cw-typing';
    wrap.innerHTML = `<div class="cw-typing"><span></span><span></span><span></span></div>`;
    msgs.appendChild(wrap);
    msgs.scrollTop = msgs.scrollHeight;
  }

  function removeTyping() {
    const el = $('cw-typing');
    if (el) el.remove();
  }

  /* ── Send ── */
  async function send(overrideText) {
    const input = $('cw-input');
    const text = overrideText || input.value.trim();
    if (!text || isBusy) return;
    input.value = '';
    $('cw-qr').innerHTML = '';
    appendMsg('user', text);
    isBusy = true;
    showTyping();

    try {
      if (!cfg.webhookUrl) throw new Error('no webhook');
      const res = await fetch(cfg.webhookUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          message: text,
          history: history.slice(-12),
          systemPrompt: cfg.systemPrompt,
          businessName: cfg.businessName
        })
      });
      if (!res.ok) throw new Error('http ' + res.status);
      const data = await res.json();
      removeTyping();
      appendMsg('bot', data.reply || data.message || data.output || 'Got it! How else can I help?');
    } catch {
      removeTyping();
      appendMsg('bot', `I'm having a little trouble right now. You can reach us directly on WhatsApp and we'll get back to you promptly! 😊`);
    }
    isBusy = false;
  }

  /* ── Events ── */
  $('cw-bubble').addEventListener('click', toggle);
  $('cw-close-btn').addEventListener('click', toggle);

  // Auto-open on page load
window.addEventListener('load', () => {
  setTimeout(() => {
    isOpen = true;
    $('cw-window').classList.add('cw-open');
    greet();
  }, 1500); // slight delay feels more natural
});

// Auto-close when visitor scrolls away or clicks elsewhere
document.addEventListener('click', (e) => {
  if (isOpen && !$('cw-root').contains(e.target)) {
    isOpen = false;
    $('cw-window').classList.remove('cw-open');
  }
});

$('cw-send').addEventListener('click', () => send());
$('cw-input').addEventListener('keydown', e => {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(); }
});

})();
