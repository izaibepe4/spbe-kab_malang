{{-- Gender Selection Popup --}}
<div id="genderPopup" class="fixed inset-0 z-50 hidden">
    {{-- Backdrop blur - blur the background content --}}
    <div class="absolute inset-0 backdrop-blur-md"></div>

    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4 transform transition-all font-['Poppins']">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-[#102A66] to-[#1C2B78] rounded-t-2xl p-6 text-center">
                <h2 class="text-2xl font-bold text-white mb-2">Selamat Datang!</h2>
            </div>

            {{-- Content --}}
            <div class="p-6">
                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-[#102A66] mb-2">Pilih Jenis Kelamin Anda</h3>
                </div>

                {{-- Gender Options - Side by Side --}}
                <div class="grid grid-cols-2 gap-4">
                    <button onclick="selectGender('male')"
                        class="flex flex-col items-center justify-center p-6 border-2 border-gray-200 rounded-xl hover:border-[#FFA84A] hover:bg-orange-50 transition-all duration-200 group">
                        <div class="w-16 h-16 border-2 border-[#102A66] rounded-full flex items-center justify-center mb-4 group-hover:border-[#FFA84A] group-hover:bg-[#FFA84A] group-hover:bg-opacity-10 transition-all duration-200">
                            <!-- Male Icon - PNG from local assets -->
                            <img src="{{ asset('assets/icons/profile.png') }}" alt="Male" class="w-10 h-10 object-contain">
                        </div>
                        <div class="text-center">
                            <div class="font-semibold text-[#102A66] group-hover:text-[#FFA84A] text-lg transition-colors duration-200">Laki-laki</div>
                            <div class="text-sm text-gray-500">Male</div>
                        </div>
                    </button>

                    <button onclick="selectGender('female')"
                        class="flex flex-col items-center justify-center p-6 border-2 border-gray-200 rounded-xl hover:border-[#102A66] hover:bg-blue-50 transition-all duration-200 group">
                        <div class="w-16 h-16 border-2 border-[#FFA84A] rounded-full flex items-center justify-center mb-4 group-hover:border-[#102A66] group-hover:bg-[#102A66] group-hover:bg-opacity-10 transition-all duration-200">
                            <!-- Female Icon - PNG from local assets -->
                            <img src="{{ asset('assets/icons/woman.png') }}" alt="Female" class="w-10 h-10 object-contain">
                        </div>
                        <div class="text-center">
                            <div class="font-semibold text-[#FFA84A] group-hover:text-[#102A66] text-lg transition-colors duration-200">Perempuan</div>
                            <div class="text-sm text-gray-500">Female</div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Loading overlay --}}
<div id="genderLoading" class="fixed inset-0 z-60 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3 font-['Poppins']">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#102A66]"></div>
            <span class="text-[#102A66]">Menyimpan data...</span>
        </div>
    </div>
</div>

<script>
    let genderSelected = false;

    // Check if gender already recorded on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, checking gender status...');
        checkGenderStatus();
    });

    function checkGenderStatus() {
        console.log('Checking gender status...');
        fetch('/visitor/check-gender')
            .then(response => {
                console.log('Response received:', response);
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                if (!data.recorded) {
                    console.log('Gender not recorded, showing popup in 1 second...');
                    // Show popup after a short delay for better UX
                    setTimeout(() => {
                        showGenderPopup();
                    }, 1000);
                } else {
                    console.log('Gender already recorded, not showing popup');
                }
            })
            .catch(error => {
                console.error('Error checking gender status:', error);
                console.log('Error occurred, showing popup anyway in 1 second...');
                // Show popup anyway if there's an error
                setTimeout(() => {
                    showGenderPopup();
                }, 1000);
            });
    }

    function showGenderPopup() {
        console.log('Attempting to show gender popup...');
        const popup = document.getElementById('genderPopup');
        if (popup) {
            console.log('Popup element found, removing hidden class');
            popup.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
            console.log('Popup should now be visible');
        } else {
            console.error('Popup element not found!');
        }
    }

    function hideGenderPopup() {
        document.getElementById('genderPopup').classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restore scrolling
    }

    function showLoading() {
        document.getElementById('genderLoading').classList.remove('hidden');
    }

    function hideLoading() {
        document.getElementById('genderLoading').classList.add('hidden');
    }

    function selectGender(gender) {
        if (genderSelected) return; // Prevent double submission

        genderSelected = true;
        showLoading();

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch('/visitor/record-gender', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    gender: gender
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    hideGenderPopup();
                    // Show thank you message
                    showThankYouMessage();
                    // Reset for next time
                    genderSelected = false;
                } else {
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                    genderSelected = false;
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
                genderSelected = false;
            });
    }

    function showThankYouMessage() {
        // Create and show a brief thank you notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
        notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            Terima kasih!
        </div>
    `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Animate out and remove
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
</script>