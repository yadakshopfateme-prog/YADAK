<?php
function isMobile($phone) {
  $phone = trim($phone);

  return preg_match('/^(09\d{9}|\+989\d{9})$/', $phone);
}
?>

<?php if (isset($phone) && isMobile($phone)): ?>
  <a class="text-sm grow text-center font-semibold text-white px-4 py-2 bg-yellow-600"
     onclick="messageModal()" href="#">
    ارسال پیامک
  </a>
<?php endif; ?>


<div id="smsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-lg w-96 p-6 text-right relative">
        <h2 class="text-xl font-bold mb-4 text-gray-700">ارسال پیامک</h2>

        <label class="block mb-2 text-sm font-medium text-gray-700">شماره موبایل:</label>
        <input id="phone" type="text" class="w-full border border-gray-300 rounded-lg p-2 mb-4 focus:ring-2 focus:ring-yellow-400 outline-none" placeholder="مثلاً 09123456789">

        <label class="block mb-2 text-sm font-medium text-gray-700">متن پیام:</label>
        <textarea id="messageInput" class="w-full border border-gray-300 rounded-lg p-2 mb-3 h-28 resize-none focus:ring-2 focus:ring-yellow-400 outline-none" placeholder="متن پیام خود را وارد کنید"></textarea>

        <!--  پیام آماده -->
        <div id="readyMessages" class="flex flex-wrap gap-2 mb-4"></div>

        <div class="flex justify-between">
            <button onclick="sendSMS()" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
                ارسال
            </button>
            <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-400 rounded-lg hover:bg-gray-200 transition">
                بستن
            </button>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('smsModal');
    const messageInput = document.getElementById('messageInput');
    const readyMessagesDiv = document.getElementById('readyMessages');

    function messageModal() {
        modal.classList.remove('hidden');
        loadMessages();
    }

    function closeModal() {
        modal.classList.add('hidden');
    }
    window.onclick = e => {
        if (e.target === modal) closeModal();
    };


    async function loadMessages() {
        const res = await fetch('messages.json');
        const messages = await res.json();
        readyMessagesDiv.innerHTML = '';
        messages.forEach(m => {
            const btn = document.createElement('button');
            btn.textContent = m.title;
            btn.className = 'bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded-lg text-sm';
            btn.onclick = () => {
                messageInput.value = m.message;
            };
            readyMessagesDiv.appendChild(btn);
        });
    }


    async function sendSMS() {
        const phone = document.getElementById('phone').value.trim();
        const message = messageInput.value.trim();

        if (!phone || !message) {
            alert('شماره و متن پیام الزامی است');
            return;
        }

        const res = await fetch('send_sms.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                phone,
                message
            })
        });

        const result = await res.json();
        if (result.status === 'success') {
            alert(' پیام با موفقیت ارسال شد');
            closeModal();
        } else {
            alert(' خطا در ارسال پیام: ' + (result.message || ''));
        }
    }

</script>

