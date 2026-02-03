(() => {
    const root = document.getElementById('chat-root');
    if (!root) {
        return;
    }

    const messagesEl = document.getElementById('chat-messages');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-message');
    const submit = document.getElementById('chat-submit');

    if (!messagesEl || !form || !input || !submit) {
        return;
    }

    const roomId = root.dataset.roomId;
    const postUrl = root.dataset.postUrl;
    const userName = root.dataset.userName || 'Usuario';
    const token = form.querySelector('input[name="_token"]')?.value || '';

    let tempCounter = 0;

    const formatTime = (value) => {
        if (value) {
            return value;
        }
        return new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    };

    const scrollToBottom = () => {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    };

    const createMessageElement = (payload, isTemp = false) => {
        const wrapper = document.createElement('div');
        wrapper.className = 'mb-2';
        wrapper.dataset.messageId = payload.id;
        if (isTemp) {
            wrapper.dataset.temp = '1';
        }

        const header = document.createElement('div');
        header.className = 'd-flex justify-content-between small text-secondary';

        const name = document.createElement('span');
        name.className = 'fw-semibold text-white';
        name.textContent = payload.user?.name || 'Usuario';

        const time = document.createElement('span');
        time.textContent = formatTime(payload.created_at);

        header.appendChild(name);
        header.appendChild(time);

        const body = document.createElement('div');
        body.className = 'small';
        body.textContent = payload.message;

        wrapper.appendChild(header);
        wrapper.appendChild(body);

        return wrapper;
    };

    const appendMessage = (payload, options = {}) => {
        if (!payload?.id) {
            return null;
        }

        if (messagesEl.querySelector(`[data-message-id="${payload.id}"]`)) {
            return null;
        }

        const element = createMessageElement(payload, options.isTemp);
        messagesEl.appendChild(element);
        scrollToBottom();
        return element;
    };

    const replaceTempMessage = (tempId, payload) => {
        const tempElement = messagesEl.querySelector(`[data-message-id="${tempId}"]`);
        if (!tempElement) {
            appendMessage(payload);
            return;
        }

        tempElement.dataset.messageId = payload.id;
        tempElement.removeAttribute('data-temp');
        const time = tempElement.querySelector('span:last-child');
        if (time) {
            time.textContent = formatTime(payload.created_at);
        }
    };

    const findMatchingTemp = (payload) => {
        const temps = Array.from(messagesEl.querySelectorAll('[data-temp="1"]'));
        if (!temps.length) {
            return null;
        }

        return temps.find((element) => {
            const text = element.querySelector('div.small')?.textContent?.trim() || '';
            const name = element.querySelector('span.fw-semibold')?.textContent?.trim() || '';
            return text === payload.message && name === (payload.user?.name || 'Usuario');
        }) || null;
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const message = input.value.trim();
        if (!message) {
            return;
        }

        submit.disabled = true;

        const tempId = `temp-${Date.now()}-${tempCounter++}`;
        appendMessage({
            id: tempId,
            message,
            user: { name: userName },
            created_at: formatTime(),
        }, { isTemp: true });

        try {
            const response = await fetch(postUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new URLSearchParams({ message }),
            });

            if (!response.ok) {
                throw new Error('Error al enviar el mensaje');
            }

            const data = await response.json();
            replaceTempMessage(tempId, data);
            input.value = '';
        } catch (error) {
            const tempElement = messagesEl.querySelector(`[data-message-id="${tempId}"]`);
            if (tempElement) {
                tempElement.classList.add('text-danger');
            }
        } finally {
            submit.disabled = false;
            input.focus();
        }
    });

    const bindEcho = () => {
        if (!window.Echo || !roomId) {
            return;
        }

        window.Echo.private(`chat.room.${roomId}`)
            .listen('ChatMessageSent', (event) => {
                const match = findMatchingTemp(event);
                if (match) {
                    replaceTempMessage(match.dataset.messageId, event);
                    return;
                }
                appendMessage(event);
            });
    };

    bindEcho();
    scrollToBottom();
})();
