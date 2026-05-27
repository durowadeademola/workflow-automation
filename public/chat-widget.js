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
 *     clientId:     'blueflow-automation',        // for multi-instance support
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
 * <script src="https://blueflowautomation.com/chat-widget.js"></script>
 */

(function () {
  'use strict';

  const cfg = Object.assign({
    webhookUrl: '',
    businessName: 'AI Assistant',
    agentName: 'AI Assistant',
    primaryColor: '#1D9E75',
    waNumber: '',
    greeting: '👋 Hello! How can I help you today?',
    systemPrompt: 'You are a helpful AI assistant. Be friendly and concise.',
    position: 'right',
    quickReplies: [],
    autoOpenDelay: 1500,
    clientId: 'default',
  }, window.ChatWidgetConfig || {});

  const $ = id => document.getElementById(id);
  const now = () => new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  const pc = cfg.primaryColor;
  const isMobile = () => window.innerWidth <= 520;

  const style = document.createElement('style');
  style.textContent = `
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&display=swap');

    #cw-root *, #cw-root *::before, #cw-root *::after {
      box-sizing: border-box; margin: 0; padding: 0;
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      -webkit-font-smoothing: antialiased;
    }
    #cw-root {
      position: fixed;
      ${cfg.position}: 20px;
      bottom: 20px;
      z-index: 2147483647;
    }

    /* ── Bubble ── */
    #cw-bubble {
      width: 60px; height: 60px; border-radius: 50%;
      background: ${pc};
      border: none; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 4px 20px ${pc}55, 0 2px 8px rgba(0,0,0,0.15);
      transition: transform .25s cubic-bezier(.34,1.56,.64,1), box-shadow .25s;
      position: relative; outline: none;
    }
    #cw-bubble:hover {
      transform: scale(1.1);
      box-shadow: 0 8px 30px ${pc}66, 0 4px 12px rgba(0,0,0,0.2);
    }
    #cw-bubble .cw-icon { position: absolute; transition: all .3s cubic-bezier(.34,1.56,.64,1); }
    #cw-bubble .cw-icon-chat { opacity: 1; transform: scale(1) rotate(0deg); }
    #cw-bubble .cw-icon-x { opacity: 0; transform: scale(0.5) rotate(-90deg); }
    #cw-bubble.is-open .cw-icon-chat { opacity: 0; transform: scale(0.5) rotate(90deg); }
    #cw-bubble.is-open .cw-icon-x { opacity: 1; transform: scale(1) rotate(0deg); }

    #cw-notif {
      position: absolute; top: -2px; ${cfg.position}: -2px;
      width: 16px; height: 16px; border-radius: 50%;
      background: #ff4757; border: 2.5px solid white;
      transition: transform .2s; animation: cwPop .4s cubic-bezier(.34,1.56,.64,1);
    }
    @keyframes cwPop { from { transform: scale(0); } to { transform: scale(1); } }

    /* ── Window ── */
    #cw-window {
      position: absolute;
      bottom: 76px;
      ${cfg.position}: 0;
      width: 370px;
      height: 540px;
      background: #ffffff;
      border-radius: 24px;
      overflow: hidden;
      display: flex; flex-direction: column;
      box-shadow: 0 24px 64px rgba(0,0,0,0.14), 0 8px 24px rgba(0,0,0,0.08), 0 0 0 0.5px rgba(0,0,0,0.06);
      transform-origin: bottom ${cfg.position};
      transform: scale(0.88) translateY(16px);
      opacity: 0; pointer-events: none;
      transition: transform .3s cubic-bezier(.34,1.56,.64,1), opacity .2s ease;
    }
    #cw-window.is-open {
      transform: scale(1) translateY(0);
      opacity: 1; pointer-events: all;
    }

    /* Mobile fullscreen */
    @media (max-width: 520px) {
      #cw-root { ${cfg.position}: 0; bottom: 0; width: 100%; }
      #cw-bubble { ${cfg.position}: 16px; bottom: 16px; position: fixed; }
      #cw-notif { ${cfg.position}: 14px; top: 12px; }
      #cw-window {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        width: 100%; height: 92vh;
        border-radius: 24px 24px 0 0;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
      }
    }

    /* ── Header ── */
    .cw-header {
      background: ${pc};
      padding: 18px 18px 16px;
      display: flex; align-items: center; gap: 12px;
      flex-shrink: 0; position: relative;
    }
    .cw-header-left { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }
    .cw-avatar-wrap { position: relative; flex-shrink: 0; }
    .cw-avatar {
      width: 42px; height: 42px; border-radius: 50%;
      background: rgba(255,255,255,0.18);
      border: 2px solid rgba(255,255,255,0.3);
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; font-weight: 600; color: white;
    }
    .cw-online-ring {
      position: absolute; bottom: 1px; right: 1px;
      width: 11px; height: 11px; border-radius: 50%;
      background: #a8f5c8; border: 2px solid ${pc};
    }
    .cw-header-text { min-width: 0; }
    .cw-header-name {
      font-size: 15px; font-weight: 600; color: white;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
      letter-spacing: -0.2px;
    }
    .cw-header-status {
      font-size: 12px; color: rgba(255,255,255,0.75);
      margin-top: 1px; display: flex; align-items: center; gap: 5px;
    }
    .cw-status-dot { width: 5px; height: 5px; border-radius: 50%; background: #a8f5c8; }
    .cw-close-btn {
      width: 32px; height: 32px; border-radius: 50%;
      background: rgba(255,255,255,0.15); border: none;
      cursor: pointer; display: flex; align-items: center; justify-content: center;
      color: white; flex-shrink: 0; transition: background .15s;
    }
    .cw-close-btn:hover { background: rgba(255,255,255,0.25); }

    /* ── Messages ── */
    #cw-messages {
      flex: 1; overflow-y: auto; overflow-x: hidden;
      padding: 18px 14px 10px;
      display: flex; flex-direction: column; gap: 14px;
      background: #f5f6fa;
      scroll-behavior: smooth;
    }
    #cw-messages::-webkit-scrollbar { width: 0px; }

    .cw-msg { display: flex; flex-direction: column; max-width: 86%; }
    .cw-msg.cw-bot { align-self: flex-start; }
    .cw-msg.cw-user { align-self: flex-end; }
    .cw-msg { animation: cwSlideIn .22s ease; }
    @keyframes cwSlideIn {
      from { opacity: 0; transform: translateY(8px) scale(0.97); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .cw-row { display: flex; align-items: flex-end; gap: 8px; }
    .cw-mini-avatar {
      width: 28px; height: 28px; border-radius: 50%;
      background: ${pc}22; border: 1.5px solid ${pc}33;
      display: flex; align-items: center; justify-content: center;
      font-size: 10px; font-weight: 600; color: ${pc};
      flex-shrink: 0;
    }

    .cw-bubble {
      display: block;
      padding: 18px 22px;
      line-height: 1.7;
      font-size: 15px;
      word-break: break-word;
      white-space: pre-wrap;
      max-width: 100%;
    }
    .cw-bot .cw-bubble {
      background: white; color: #1a1c23;
      border-radius: 20px 20px 20px 5px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.06), 0 0 0 1px rgba(0,0,0,0.04);
    }
    .cw-user .cw-bubble {
      background: ${pc};
      color: white;
      border-radius: 18px 18px 4px 18px;
    }
    .cw-msg-time {
      font-size: 10.5px; color: #b8bcc8;
      margin-top: 5px; padding: 0 5px;
    }
    .cw-user .cw-msg-time { align-self: flex-end; }

    .cw-read-more {
      display: inline-flex; align-items: center; gap: 6px;
      font-size: 12.5px; font-weight: 500;
      color: ${pc}; text-decoration: none;
      margin-top: 8px; padding: 6px 14px;
      border: 1.5px solid ${pc}30;
      border-radius: 20px; background: ${pc}08;
      transition: all .15s; width: fit-content;
    }
    .cw-read-more:hover { background: ${pc}18; border-color: ${pc}55; transform: translateY(-1px); }
    .cw-read-more svg { flex-shrink: 0; }

    /* Typing */
    #cw-typing-msg { align-self: flex-start; animation: cwSlideIn .22s ease; }
    .cw-typing-bubble {
      background: white;
      border-radius: 18px 18px 18px 5px;
      padding: 13px 18px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.07), 0 0 0 0.5px rgba(0,0,0,0.05);
      display: flex; gap: 5px; align-items: center;
    }
    .cw-typing-bubble span {
      width: 7px; height: 7px; border-radius: 50%; background: #c8ccd6;
      animation: cwDot 1.4s infinite ease-in-out;
    }
    .cw-typing-bubble span:nth-child(2) { animation-delay: .16s; }
    .cw-typing-bubble span:nth-child(3) { animation-delay: .32s; }
    @keyframes cwDot {
      0%, 60%, 100% { transform: translateY(0); background: #c8ccd6; }
      30% { transform: translateY(-7px); background: ${pc}; }
    }

    /* WhatsApp */
    .cw-wa {
      display: inline-flex; align-items: center; gap: 8px;
      background: #25d366; color: white; text-decoration: none;
      border-radius: 12px; padding: 9px 16px; margin-top: 8px;
      font-size: 13px; font-weight: 600;
      transition: background .15s, transform .15s;
      width: fit-content;
    }
    .cw-wa:hover { background: #20bd5a; transform: translateY(-1px); }

    /* Quick replies */
    #cw-qr {
      display: flex; flex-wrap: wrap; gap: 8px;
      padding: 4px 14px 12px; background: #f5f6fa;
    }
    .cw-qr {
      background: white; color: ${pc};
      border: 1.5px solid ${pc}30;
      border-radius: 24px; padding: 7px 16px;
      font-size: 13px; font-weight: 500; cursor: pointer;
      transition: all .18s; white-space: nowrap;
      box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .cw-qr:hover { background: ${pc}; color: white; border-color: ${pc}; transform: translateY(-1px); box-shadow: 0 4px 12px ${pc}33; }

    /* Input */
    #cw-input-bar {
      display: flex; align-items: center; gap: 10px;
      padding: 12px 14px 14px;
      background: white;
      border-top: 0.5px solid rgba(0,0,0,0.07);
      flex-shrink: 0;
    }
    #cw-input {
      flex: 1; background: #f5f6fa;
      border: 1.5px solid transparent; border-radius: 24px;
      padding: 10px 16px; font-size: 14px; color: #1a1c23;
      outline: none; transition: all .2s;
      font-family: inherit;
    }
    #cw-input:focus { background: white; border-color: ${pc}55; box-shadow: 0 0 0 4px ${pc}12; }
    #cw-input::placeholder { color: #b8bcc8; }
    #cw-send {
      width: 42px; height: 42px; border-radius: 50%;
      background: ${pc}; border: none; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      box-shadow: 0 2px 10px ${pc}44;
      transition: transform .2s cubic-bezier(.34,1.56,.64,1), box-shadow .2s;
    }
    #cw-send:hover { transform: scale(1.1); box-shadow: 0 4px 16px ${pc}55; }
    #cw-send:active { transform: scale(0.93); }

    /* Footer */
    #cw-footer {
      text-align: center; font-size: 11px; color: #c0c4ce;
      padding: 0 0 10px; background: white; flex-shrink: 0;
      letter-spacing: 0.1px;
    }
    #cw-footer a { color: #25d366; text-decoration: none; font-weight: 500; }

    /* Date divider */
    .cw-divider {
      display: flex; align-items: center; gap: 10px;
      font-size: 11px; color: #b8bcc8; margin: 4px 0;
    }
    .cw-divider::before, .cw-divider::after {
      content: ''; flex: 1; height: 0.5px; background: rgba(0,0,0,0.08);
    }
  `;
  document.head.appendChild(style);

  const root = document.createElement('div');
  root.id = 'cw-root';
  root.innerHTML = `
    <div id="cw-window" role="dialog" aria-label="Chat with ${cfg.agentName}">
      <div class="cw-header">
        <div class="cw-header-left">
          <div class="cw-avatar-wrap">
            <div class="cw-avatar">${cfg.agentName.charAt(0).toUpperCase()}</div>
            <div class="cw-online-ring"></div>
          </div>
          <div class="cw-header-text">
            <div class="cw-header-name">${cfg.agentName}</div>
            <div class="cw-header-status">
              online
            </div>
          </div>
        </div>
        <button class="cw-close-btn" id="cw-close-btn" aria-label="Close chat">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>
      <div id="cw-messages" role="log" aria-live="polite"></div>
      <div id="cw-qr"></div>
      <div id="cw-input-bar">
        <input type="text" id="cw-input" placeholder="Type a message..." autocomplete="off" aria-label="Chat message" />
        <button id="cw-send" aria-label="Send">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
            <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z"/>
          </svg>
        </button>
      </div>
      <div id="cw-footer">
        Powered by <a href="https://blueflowautomation.com" target="_blank" rel="noopener" style="color:inherit;text-decoration:none;font-weight:600;">Blueflow</a> &nbsp;·&nbsp;
        <a href="https://wa.me/${cfg.waNumber}" target="_blank" rel="noopener">Send us a message</a>
      </div>
    </div>

    <button id="cw-bubble" aria-label="Open chat assistant">
      <span class="cw-icon cw-icon-chat">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
      </span>
      <span class="cw-icon cw-icon-x">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </span>
    </button>
    <div id="cw-notif"></div>
  `;
  document.body.appendChild(root);

  let isOpen = false;
  let isBusy = false;
  let history = [];
  let greeted = false;

  function toggle() {
    isOpen = !isOpen;
    $('cw-window').classList.toggle('is-open', isOpen);
    $('cw-bubble').classList.toggle('is-open', isOpen);
    if (isOpen) {
      const notif = $('cw-notif');
      if (notif) notif.remove();
      if (!greeted) { setTimeout(greet, 300); greeted = true; }
      setTimeout(() => $('cw-input').focus(), 350);
    }
  }

  function greet() {
    addDivider('Today');
    appendMsg('bot', cfg.greeting);
    if (cfg.quickReplies.length) renderQR(cfg.quickReplies);
  }

  function addDivider(label) {
    const msgs = $('cw-messages');
    const d = document.createElement('div');
    d.className = 'cw-divider';
    d.textContent = label;
    msgs.appendChild(d);
  }

  function renderQR(items) {
    const qr = $('cw-qr');
    qr.innerHTML = '';
    items.forEach(label => {
      const btn = document.createElement('button');
      btn.className = 'cw-qr';
      btn.textContent = label;
      btn.onclick = () => { qr.innerHTML = ''; send(label); };
      qr.appendChild(btn);
    });
  }

  function appendMsg(role, text, data = {}) {
    const msgs = $('cw-messages');
    const wrap = document.createElement('div');
    wrap.className = `cw-msg cw-${role}`;

    if (role === 'bot') {
      const row = document.createElement('div');
      row.className = 'cw-row';
      const av = document.createElement('div');
      av.className = 'cw-mini-avatar';
      av.textContent = cfg.agentName.charAt(0).toUpperCase();
      const bub = document.createElement('div');
      bub.className = 'cw-bubble';
      bub.textContent = text;
      row.appendChild(av);
      row.appendChild(bub);
      wrap.appendChild(row);

      if (data.sourceUrl) {
        const readMore = document.createElement('a');
        readMore.href = data.sourceUrl;
        readMore.target = '_blank';
        readMore.rel = 'noopener';
        readMore.className = 'cw-read-more';
        readMore.innerHTML = `
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
          <polyline points="15 3 21 3 21 9"/>
          <line x1="10" y1="14" x2="21" y2="3"/>
          </svg> Read more
        `;
        wrap.appendChild(readMore);
      }

    } else {
      const bub = document.createElement('div');
      bub.className = 'cw-bubble';
      bub.textContent = text;
      wrap.appendChild(bub);
    }

    const t = document.createElement('div');
    t.className = 'cw-msg-time';
    t.textContent = now();
    wrap.appendChild(t);

    msgs.appendChild(wrap);
    msgs.scrollTop = msgs.scrollHeight;
    history.push({ role: role === 'bot' ? 'assistant' : 'user', content: text });
  }

  function showTyping() {
    const msgs = $('cw-messages');
    const wrap = document.createElement('div');
    wrap.className = 'cw-row';
    wrap.id = 'cw-typing-msg';
    const av = document.createElement('div');
    av.className = 'cw-mini-avatar';
    av.textContent = cfg.agentName.charAt(0).toUpperCase();
    const bub = document.createElement('div');
    bub.className = 'cw-typing-bubble';
    bub.innerHTML = '<span></span><span></span><span></span>';
    wrap.appendChild(av);
    wrap.appendChild(bub);
    msgs.appendChild(wrap);
    msgs.scrollTop = msgs.scrollHeight;
  }

  function removeTyping() {
    const el = $('cw-typing-msg');
    if (el) el.remove();
  }

  async function send(override) {
    const input = $('cw-input');
    const text = override || input.value.trim();
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
          businessName: cfg.businessName,
          clientId: cfg.clientId,
          waNumber: cfg.waNumber
        })
      });
      if (!res.ok) throw new Error('http ' + res.status);
      const data = await res.json();
      removeTyping();
      appendMsg('bot', data.reply || data.message || data.output || 'How else can I help?', data);
    } catch {
      removeTyping();
      appendMsg('bot', `Sorry, I'm having a little trouble right now. You can reach us directly on WhatsApp and we'll assist you promptly. 😊`);
    }
    isBusy = false;
  }

  $('cw-bubble').addEventListener('click', toggle);
  $('cw-close-btn').addEventListener('click', toggle);
  $('cw-send').addEventListener('click', () => send());
  $('cw-input').addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(); }
  });
  document.addEventListener('click', e => {
    if (isOpen && !$('cw-root').contains(e.target)) {
      isOpen = false;
      $('cw-window').classList.remove('is-open');
      $('cw-bubble').classList.remove('is-open');
    }
  });
  window.addEventListener('load', () => {
    setTimeout(() => { if (!isOpen) toggle(); }, cfg.autoOpenDelay ?? 1500);
  });

  window.ChatWidget = {
    toggle,
    open: () => { if (!isOpen) toggle(); },
    close: () => { if (isOpen) toggle(); },
    destroy: () => root.remove(),
    clearHistory: () => { history = []; }
  };

})();
