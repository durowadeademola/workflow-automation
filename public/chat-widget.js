/**
 * AI Chat Widget — Embeddable Script
 * -----------------------------------
 * Drop this script at the bottom of any HTML page.
 * Configure via window.ChatWidgetConfig before the script loads.
 *
 * USAGE:
 * <script>
 *   window.ChatWidgetConfig = {
 *     clientId:     123,                    // the numeric Client id in Blueflow's admin — REQUIRED
 *     apiBase:      'https://blueflowautomation.com/api',
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
 *
 * The widget never talks to n8n directly — every message goes to
 * `{apiBase}/widget/chat`, which looks up this client's n8n webhook URL
 * server-side (set on the Client record in the admin panel) and checks
 * they have an active subscription before forwarding anything. This is
 * what makes the widget actually stop working if a client's subscription
 * lapses, rather than just hiding a button in the UI.
 *
 * HUMAN HANDOFF:
 * Every message includes a `sessionToken` (stable per visitor, persisted
 * in localStorage). When your n8n workflow detects the visitor wants a
 * human, call `POST {apiBase}/widget/conversations` with that same session
 * token (see the Laravel API for the exact contract), then reply with:
 *   { reply: "Connecting you now...", handoff: true, conversationId: 123, lastMessageId: 45 }
 * The widget then polls Laravel directly for the agent's replies.
 */

(function () {
  'use strict';

  const cfg = Object.assign({
    apiBase: 'https://blueflowautomation.com/api',
    businessName: 'AI Assistant',
    agentName: 'AI Assistant',
    primaryColor: '#1D9E75',
    waNumber: '',
    greeting: '👋 Hello! How can I help you today?',
    systemPrompt: 'You are a helpful AI assistant. Be friendly and concise.',
    position: 'right',
    quickReplies: [],
    autoOpenDelay: 1500,
    clientId: null,
  }, window.ChatWidgetConfig || {});

  const $ = id => document.getElementById(id);
  const now = () => new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  const isMobile = () => window.innerWidth <= 520;

  // ── Theme: derive a two-tone gradient from the single configured brand
  // colour, so every installation gets a premium look without needing to
  // configure a second colour.
  function hexToRgb(hex) {
    const clean = String(hex).replace('#', '');
    const full = clean.length === 3 ? clean.split('').map(c => c + c).join('') : clean;
    const num = parseInt(full, 16) || 0;
    return { r: (num >> 16) & 255, g: (num >> 8) & 255, b: num & 255 };
  }
  function shade(hex, percent) {
    const { r, g, b } = hexToRgb(hex);
    const t = percent < 0 ? 0 : 255;
    const p = Math.abs(percent);
    const mix = c => Math.round(c + (t - c) * p).toString(16).padStart(2, '0');
    return `#${mix(r)}${mix(g)}${mix(b)}`;
  }

  const pc = cfg.primaryColor;
  const pcDark = shade(pc, -0.28);
  const grad = `linear-gradient(135deg, ${pc}, ${pcDark})`;

  // Persistent per-visitor identifier, so a human handoff can be tied back
  // to this browser across messages (and survives a page refresh).
  function getSessionToken() {
    const KEY = 'cw_session_token';
    try {
      let token = window.localStorage.getItem(KEY);
      if (!token) {
        token = window.crypto && window.crypto.randomUUID
          ? window.crypto.randomUUID()
          : `cw-${Math.random().toString(36).slice(2)}${Date.now().toString(36)}`;
        window.localStorage.setItem(KEY, token);
      }
      return token;
    } catch (e) {
      return `cw-${Math.random().toString(36).slice(2)}${Date.now().toString(36)}`;
    }
  }
  const sessionToken = getSessionToken();

  const style = document.createElement('style');
  style.textContent = `
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

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
      width: 62px; height: 62px; border-radius: 50%;
      background: ${grad};
      border: none; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 10px 26px ${pc}4d, 0 3px 10px rgba(0,0,0,0.18);
      transition: transform .25s cubic-bezier(.34,1.56,.64,1), box-shadow .25s;
      position: relative; outline: none;
    }
    #cw-bubble::before {
      content: '';
      position: absolute; inset: 0; border-radius: 50%;
      background: linear-gradient(135deg, rgba(255,255,255,0.32), transparent 55%);
      pointer-events: none;
    }
    #cw-bubble:hover {
      transform: scale(1.08);
      box-shadow: 0 14px 34px ${pc}5c, 0 5px 14px rgba(0,0,0,0.22);
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
      z-index: 1;
    }
    #cw-notif::after {
      content: '';
      position: absolute; inset: -5px;
      border-radius: 50%;
      border: 2px solid #ff4757;
      animation: cwPulseRing 1.8s cubic-bezier(.2,.7,.4,1) infinite;
    }
    @keyframes cwPulseRing {
      0% { transform: scale(0.75); opacity: 0.7; }
      100% { transform: scale(1.9); opacity: 0; }
    }

    /* ── Window ── */
    #cw-window {
      position: absolute;
      bottom: 78px;
      ${cfg.position}: 0;
      width: 374px;
      height: 552px;
      background: #ffffff;
      border-radius: 26px;
      overflow: hidden;
      display: flex; flex-direction: column;
      box-shadow: 0 30px 70px -12px rgba(0,0,0,0.22), 0 10px 28px -6px rgba(0,0,0,0.12), 0 0 0 1px rgba(0,0,0,0.04);
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
      background: ${grad};
      padding: 20px 18px 17px;
      display: flex; align-items: center; gap: 12px;
      flex-shrink: 0; position: relative;
    }
    .cw-header::before {
      content: '';
      position: absolute; top: -34px; right: -18px;
      width: 130px; height: 130px; border-radius: 50%;
      background: rgba(255,255,255,0.08);
      pointer-events: none;
    }
    .cw-header::after {
      content: '';
      position: absolute; bottom: -40px; left: 40%;
      width: 90px; height: 90px; border-radius: 50%;
      background: rgba(255,255,255,0.06);
      pointer-events: none;
    }
    .cw-header-left { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; position: relative; z-index: 1; }
    .cw-avatar-wrap { position: relative; flex-shrink: 0; }
    .cw-avatar {
      width: 44px; height: 44px; border-radius: 50%;
      background: rgba(255,255,255,0.2);
      border: 2px solid rgba(255,255,255,0.32);
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.25), 0 2px 8px rgba(0,0,0,0.1);
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; font-weight: 700; color: white;
      letter-spacing: 0.2px;
    }
    .cw-online-ring {
      position: absolute; bottom: 0px; right: 0px;
      width: 12px; height: 12px; border-radius: 50%;
      background: #4ade80; border: 2.5px solid ${pcDark};
      box-shadow: 0 0 0 1px rgba(255,255,255,0.15);
    }
    .cw-header-text { min-width: 0; position: relative; z-index: 1; }
    .cw-header-name {
      font-size: 15px; font-weight: 700; color: white;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
      letter-spacing: -0.2px;
    }
    .cw-header-status {
      font-size: 12px; color: rgba(255,255,255,0.8);
      margin-top: 2px; display: flex; align-items: center; gap: 5px;
    }
    .cw-status-dot { width: 5px; height: 5px; border-radius: 50%; background: #4ade80; flex-shrink: 0; }
    .cw-close-btn {
      width: 32px; height: 32px; border-radius: 50%;
      background: rgba(255,255,255,0.15); border: none;
      cursor: pointer; display: flex; align-items: center; justify-content: center;
      color: white; flex-shrink: 0; transition: background .15s, transform .15s;
      position: relative; z-index: 1;
    }
    .cw-close-btn:hover { background: rgba(255,255,255,0.26); transform: rotate(90deg); }

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
      box-shadow: 0 1px 3px rgba(0,0,0,0.06);
      display: flex; align-items: center; justify-content: center;
      font-size: 10px; font-weight: 700; color: ${pcDark};
      flex-shrink: 0;
    }

    .cw-bubble {
      display: block;
      padding: 16px 20px;
      line-height: 1.65;
      font-size: 14.5px;
      word-break: break-word;
      overflow-wrap: break-word;
      white-space: pre-wrap;
      max-width: 100%;
    }
    .cw-bot .cw-bubble {
      background: white; color: #1a1c23;
      border-radius: 18px 18px 18px 5px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.06), 0 0 0 1px rgba(0,0,0,0.04);
    }
    .cw-user .cw-bubble {
      background: ${grad};
      color: white;
      border-radius: 18px 18px 4px 18px;
      box-shadow: 0 4px 12px ${pc}33;
    }
    .cw-msg-time {
      font-size: 10.5px; color: #b8bcc8;
      margin-top: 5px; padding: 0 5px;
      display: inline-flex; align-items: center; gap: 3px;
    }
    .cw-user .cw-msg-time { align-self: flex-end; }
    .cw-tick { color: ${pc}; flex-shrink: 0; }

    .cw-read-more {
      display: inline-flex; align-items: center; gap: 6px;
      font-size: 12.5px; font-weight: 500;
      color: ${pcDark}; text-decoration: none;
      margin-top: 8px; padding: 6px 14px;
      border: 1.5px solid ${pc}30;
      border-radius: 20px; background: ${pc}0c;
      transition: all .15s; width: fit-content;
    }
    .cw-read-more:hover { background: ${pc}1e; border-color: ${pc}55; transform: translateY(-1px); }
    .cw-read-more svg { flex-shrink: 0; }

    /* Typing */
    #cw-typing-msg { align-self: flex-start; animation: cwSlideIn .22s ease; }
    .cw-typing-bubble {
      background: white;
      border-radius: 18px 18px 18px 5px;
      padding: 14px 18px;
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
      background: white; color: ${pcDark};
      border: 1.5px solid ${pc}30;
      border-radius: 24px; padding: 7px 16px;
      font-size: 13px; font-weight: 500; cursor: pointer;
      transition: all .18s; white-space: nowrap;
      box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .cw-qr:hover { background: ${grad}; color: white; border-color: transparent; transform: translateY(-1px); box-shadow: 0 4px 12px ${pc}40; }

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
      background: ${grad}; border: none; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      box-shadow: 0 3px 12px ${pc}4d;
      transition: transform .2s cubic-bezier(.34,1.56,.64,1), box-shadow .2s, opacity .2s;
    }
    #cw-send:hover { transform: scale(1.1); box-shadow: 0 5px 18px ${pc}5c; }
    #cw-send:active { transform: scale(0.93); }
    #cw-send.is-disabled { opacity: 0.38; cursor: default; pointer-events: none; box-shadow: none; }

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

    @media (prefers-reduced-motion: reduce) {
      #cw-root *, #cw-root *::before, #cw-root *::after {
        animation-duration: 0.001ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.001ms !important;
      }
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
              <span class="cw-status-dot"></span>
              Online now
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
        <button id="cw-send" class="is-disabled" aria-label="Send">
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
  let handoff = null; // { conversationId, afterId, pollTimer } once a human has taken over

  const TICK_SVG = `<svg class="cw-tick" width="14" height="10" viewBox="0 0 16 11" fill="none"><path d="M1 5.5L4.5 9L10 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 5.5L9.5 9L15 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>`;

  function showNotifBadge() {
    if (isOpen || $('cw-notif')) return;
    const notif = document.createElement('div');
    notif.id = 'cw-notif';
    root.appendChild(notif);
  }

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
      btn.onclick = (e) => { e.stopPropagation(); qr.innerHTML = ''; send(label); };
      qr.appendChild(btn);
    });
  }

  function appendMsg(role, text, data = {}, senderLabel) {
    const msgs = $('cw-messages');
    const wrap = document.createElement('div');
    wrap.className = `cw-msg cw-${role}`;

    if (role === 'bot') {
      const row = document.createElement('div');
      row.className = 'cw-row';
      const av = document.createElement('div');
      av.className = 'cw-mini-avatar';
      av.textContent = (senderLabel || cfg.agentName).charAt(0).toUpperCase();
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
    t.innerHTML = role === 'user' ? `${now()} ${TICK_SVG}` : now();
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

  function updateSendState() {
    const hasText = $('cw-input').value.trim().length > 0;
    $('cw-send').classList.toggle('is-disabled', !hasText || isBusy);
  }

  // ── Human handoff ──
  // Once n8n signals a handoff, the AI steps back: further visitor messages
  // go straight to Laravel instead of the AI webhook, and we poll for the
  // agent's replies since the widget has no persistent connection.
  function enterHandoffMode(conversationId, lastMessageId) {
    handoff = { conversationId, afterId: lastMessageId || 0, pollTimer: null };
    startPolling();
  }

  function startPolling() {
    if (!handoff || handoff.pollTimer) return;
    handoff.pollTimer = setInterval(pollForMessages, 4000);
  }

  function stopPolling() {
    if (handoff && handoff.pollTimer) {
      clearInterval(handoff.pollTimer);
      handoff.pollTimer = null;
    }
  }

  async function pollForMessages() {
    if (!handoff) return;
    try {
      const url = `${cfg.apiBase}/widget/conversations/${handoff.conversationId}/messages`
        + `?token=${encodeURIComponent(sessionToken)}&after_id=${handoff.afterId}`;
      const res = await fetch(url);

      if (res.status === 402) {
        // Subscription lapsed mid-conversation — stop politely rather than
        // failing silently forever.
        appendMsg('bot', "This conversation is no longer available. Please reach out to us directly.");
        stopPolling();
        handoff = null;
        return;
      }

      if (!res.ok) return;
      const data = await res.json();

      (data.messages || []).forEach(m => {
        handoff.afterId = m.id;
        if (m.sender_type === 'agent') {
          appendMsg('bot', m.content, {}, m.sender_name);
          if (!isOpen) showNotifBadge();
        }
      });

      if (data.status === 'closed') {
        appendMsg('bot', "This conversation has ended. Feel free to start a new one anytime!");
        stopPolling();
        handoff = null;
      }
    } catch (e) {
      // transient network error — just try again on the next tick
    }
  }

  async function send(override) {
    const input = $('cw-input');
    const text = override || input.value.trim();
    if (!text || isBusy) return;
    input.value = '';
    updateSendState();
    $('cw-qr').innerHTML = '';
    appendMsg('user', text);
    isBusy = true;

    // A human has already taken over — send straight to them, skip the AI.
    if (handoff) {
      try {
        await fetch(`${cfg.apiBase}/widget/conversations/${handoff.conversationId}/messages`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ token: sessionToken, content: text })
        });
      } catch (e) {
        // the next poll will still pick up any agent reply even if this errors
      }
      isBusy = false;
      updateSendState();
      return;
    }

    showTyping();
    try {
      if (!cfg.clientId) throw new Error('no clientId configured');
      const res = await fetch(`${cfg.apiBase}/widget/chat`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          message: text,
          history: history.slice(-12),
          systemPrompt: cfg.systemPrompt,
          businessName: cfg.businessName,
          clientId: cfg.clientId,
          waNumber: cfg.waNumber,
          sessionToken: sessionToken
        })
      });
      const data = await res.json().catch(() => ({}));
      removeTyping();

      if (!res.ok) {
        // The proxy still sends a friendly, situation-specific message even
        // on a non-2xx response (e.g. subscription paused, n8n unreachable).
        appendMsg('bot', data.reply || `Sorry, I'm having a little trouble right now. You can reach us directly on WhatsApp and we'll assist you promptly. 😊`);
      } else {
        appendMsg('bot', data.reply || data.message || data.output || 'How else can I help?', data);
        if (data.handoff && data.conversationId) {
          enterHandoffMode(data.conversationId, data.lastMessageId);
        }
      }
    } catch {
      removeTyping();
      appendMsg('bot', `Sorry, I'm having a little trouble right now. You can reach us directly on WhatsApp and we'll assist you promptly. 😊`);
    }
    isBusy = false;
  }

  $('cw-bubble').addEventListener('click', toggle);
  $('cw-close-btn').addEventListener('click', toggle);
  $('cw-send').addEventListener('click', () => send());
  $('cw-input').addEventListener('input', updateSendState);
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
    destroy: () => { stopPolling(); root.remove(); },
    clearHistory: () => { history = []; }
  };

})();
