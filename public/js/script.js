// ===============================================
// Studio Clipagem - Modern JavaScript
// ===============================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initNavigation();
    initSmoothScrolling();
    initFormHandling();
    initScrollEffects();
    initAnimations();
    initModernInteractions();
    initParallaxEffects();
    initLoadingStates();
    initHeroDashboard();
});

// ===============================================
// Navigation Functions
// ===============================================

function initNavigation() {
    const navToggle = document.getElementById('nav-toggle');
    const navMenu = document.getElementById('nav-menu');
    const navLinks = document.querySelectorAll('.nav-link');

    // Toggle mobile menu
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            
            // Animate hamburger lines
            const spans = navToggle.querySelectorAll('span');
            if (navMenu.classList.contains('active')) {
                spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                spans[1].style.opacity = '0';
                spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
            } else {
                spans[0].style.transform = 'rotate(0) translate(0, 0)';
                spans[1].style.opacity = '1';
                spans[2].style.transform = 'rotate(0) translate(0, 0)';
            }
        });

        // Close menu when clicking on links
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (navMenu.classList.contains('active')) {
                    navMenu.classList.remove('active');
                    const spans = navToggle.querySelectorAll('span');
                    spans[0].style.transform = 'rotate(0) translate(0, 0)';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = 'rotate(0) translate(0, 0)';
                }
            });
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
                if (navMenu.classList.contains('active')) {
                    navMenu.classList.remove('active');
                    const spans = navToggle.querySelectorAll('span');
                    spans[0].style.transform = 'rotate(0) translate(0, 0)';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = 'rotate(0) translate(0, 0)';
                }
            }
        });
    }

    // Enhanced navbar scroll effect
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        let lastScrollY = window.scrollY;
        
        window.addEventListener('scroll', throttle(function() {
            const currentScrollY = window.scrollY;
            
            if (currentScrollY > 100) {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                navbar.style.backdropFilter = 'blur(20px)';
                navbar.style.boxShadow = '0 8px 32px rgba(0, 0, 0, 0.1)';
                navbar.style.borderBottom = '1px solid rgba(241, 146, 110, 0.2)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                navbar.style.backdropFilter = 'blur(20px)';
                navbar.style.boxShadow = 'none';
                navbar.style.borderBottom = '1px solid rgba(241, 146, 110, 0.1)';
            }

            lastScrollY = currentScrollY;
        }, 16));
    }

    // Active navigation link highlighting
    updateActiveNavLink();
    window.addEventListener('scroll', throttle(updateActiveNavLink, 16));
}

function updateActiveNavLink() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link[href^="#"]');
    
    let currentSection = '';
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 150;
        const sectionHeight = section.offsetHeight;
        
        if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
            currentSection = section.getAttribute('id');
        }
    });

    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${currentSection}`) {
            link.classList.add('active');
        }
    });
}

// ===============================================
// Hero Dashboard Animation
// ===============================================

function initHeroDashboard() {
    const dashboard = document.querySelector('.media-dashboard');
    const mediaCards = document.querySelectorAll('.media-card');
    const chartBars = document.querySelectorAll('.bar');
    
    if (dashboard) {
        // Animate dashboard on load
        setTimeout(() => {
            dashboard.style.opacity = '1';
            dashboard.style.transform = 'translateY(0)';
        }, 500);
        
        // Animate media cards
        mediaCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 700 + (index * 100));
        });
        
        // Animate chart bars
        chartBars.forEach((bar, index) => {
            setTimeout(() => {
                bar.style.opacity = '1';
                const height = bar.style.height;
                bar.style.height = '0';
                setTimeout(() => {
                    bar.style.height = height;
                }, 50);
            }, 1000 + (index * 50));
        });
        
        // Add real-time data simulation
        setInterval(() => {
            updateDashboardData();
        }, 3000);
    }
}

function updateDashboardData() {
    const counts = document.querySelectorAll('.card-count');
    const trends = document.querySelectorAll('.card-trend');
    
    counts.forEach(count => {
        const currentValue = parseInt(count.textContent.replace(/[^\d]/g, ''));
        const variation = Math.floor(Math.random() * 20) - 10;
        const newValue = Math.max(0, currentValue + variation);
        
        // Format number
        let formattedValue;
        if (newValue >= 1000) {
            formattedValue = (newValue / 1000).toFixed(1) + 'k';
        } else {
            formattedValue = newValue.toString();
        }
        
        count.textContent = formattedValue;
    });
    
    trends.forEach(trend => {
        const variation = Math.floor(Math.random() * 30) + 1;
        const isPositive = Math.random() > 0.3; // 70% chance of positive trend
        trend.textContent = (isPositive ? '+' : '-') + variation + '%';
        trend.style.color = isPositive ? '#10b981' : '#ef4444';
    });
}

// ===============================================
// Enhanced Smooth Scrolling
// ===============================================

function initSmoothScrolling() {
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                const headerHeight = document.querySelector('.navbar').offsetHeight;
                const targetPosition = targetElement.offsetTop - headerHeight - 20;
                
                // Smooth scroll with easing
                smoothScrollTo(targetPosition, 800);
            }
        });
    });
    
    // Scroll indicator
    const scrollIndicator = document.querySelector('.scroll-indicator');
    if (scrollIndicator) {
        scrollIndicator.addEventListener('click', function() {
            const servicesSection = document.getElementById('servicos');
            if (servicesSection) {
                const headerHeight = document.querySelector('.navbar').offsetHeight;
                const targetPosition = servicesSection.offsetTop - headerHeight;
                smoothScrollTo(targetPosition, 1000);
            }
        });
    }
}

function smoothScrollTo(target, duration) {
    const start = window.pageYOffset;
    const distance = target - start;
    let startTime = null;

    function animation(currentTime) {
        if (startTime === null) startTime = currentTime;
        const timeElapsed = currentTime - startTime;
        const run = easeInOutQuad(timeElapsed, start, distance, duration);
        window.scrollTo(0, run);
        if (timeElapsed < duration) requestAnimationFrame(animation);
    }

    function easeInOutQuad(t, b, c, d) {
        t /= d / 2;
        if (t < 1) return c / 2 * t * t + b;
        t--;
        return -c / 2 * (t * (t - 2) - 1) + b;
    }

    requestAnimationFrame(animation);
}

// ===============================================
// Modern Interactions
// ===============================================

function initModernInteractions() {
    // Add hover effects to cards
    const cards = document.querySelectorAll('.service-card, .stat-card, .media-card');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            if (!this.classList.contains('featured')) {
                this.style.transform = 'translateY(-10px)';
                this.style.transition = 'all 0.3s ease-out';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            if (!this.classList.contains('featured')) {
                this.style.transform = 'translateY(0)';
            }
        });
    });

    // Add magnetic effect to buttons
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
        
        // Add ripple effect
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            `;
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Service links hover effect
    const serviceLinks = document.querySelectorAll('.service-link');
    serviceLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            const arrow = this.querySelector('i');
            if (arrow) {
                arrow.style.transform = 'translateX(5px)';
            }
        });
        
        link.addEventListener('mouseleave', function() {
            const arrow = this.querySelector('i');
            if (arrow) {
                arrow.style.transform = 'translateX(0)';
            }
        });
    });

    // Add CSS for ripple animation
    if (!document.querySelector('#ripple-styles')) {
        const style = document.createElement('style');
        style.id = 'ripple-styles';
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
}

// ===============================================
// Enhanced Parallax Effects
// ===============================================

function initParallaxEffects() {
    const hero = document.querySelector('.hero');
    const heroShapes = document.querySelectorAll('.shape');
    
    if (hero) {
        window.addEventListener('scroll', throttle(function() {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            
            // Parallax for hero shapes
            heroShapes.forEach((shape, index) => {
                const speed = 0.2 + (index * 0.1);
                const yPos = scrolled * speed;
                shape.style.transform = `translateY(${yPos}px)`;
            });
        }, 16));
    }

    // Floating animation for dashboard elements
    const dashboardElements = document.querySelectorAll('.media-card, .chart-bars .bar');
    dashboardElements.forEach((element, index) => {
        element.style.animationDelay = `${index * 0.1}s`;
    });
}

// ===============================================
// Enhanced Scroll Effects
// ===============================================

function initScrollEffects() {
    // Enhanced Intersection Observer
    const observerOptions = {
        threshold: [0.1, 0.3, 0.5],
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const delay = entry.target.dataset.delay || 0;
                
                setTimeout(() => {
                    entry.target.classList.add('animate-on-scroll', 'animated');
                    
                    // Add staggered animation for grid items
                    if (entry.target.classList.contains('services-grid')) {
                        const children = entry.target.children;
                        Array.from(children).forEach((child, index) => {
                            setTimeout(() => {
                                child.classList.add('fade-in');
                            }, index * 100);
                        });
                    }
                }, delay);
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    const animateElements = document.querySelectorAll(`
        .service-card, 
        .feature-card,
        .stat-card, 
        .contact-method,
        .section-header,
        .contact-form-container,
        .services-grid,
        .services-features,
        .features-grid,
        .about-content,
        .contact-content
    `);
    
    animateElements.forEach((el, index) => {
        el.dataset.delay = index * 50;
        observer.observe(el);
    });

    // Enhanced animations for services and features
    const servicesGrid = document.querySelector('.services-grid');
    const featuresGrid = document.querySelector('.features-grid');
    
    if (servicesGrid) {
        observer.observe(servicesGrid);
    }
    
    if (featuresGrid) {
        observer.observe(featuresGrid);
    }

    // Progress indicator
    createScrollProgress();
}

function createScrollProgress() {
    const progressBar = document.createElement('div');
    progressBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(135deg, #f1926e 0%, #e8744a 100%);
        z-index: 9999;
        transition: width 0.1s ease-out;
    `;
    document.body.appendChild(progressBar);

    window.addEventListener('scroll', throttle(function() {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        progressBar.style.width = scrolled + '%';
    }, 16));
}

// ===============================================
// Loading States
// ===============================================

function initLoadingStates() {
    // Add loading animation to page
    const loader = document.createElement('div');
    loader.id = 'page-loader';
    loader.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #f1926e 0%, #e8744a 50%, #667eea 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        transition: opacity 0.5s ease-out;
    `;
    
    loader.innerHTML = `
        <div style="
            width: 60px;
            height: 60px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        "></div>
    `;
    
    document.body.appendChild(loader);
    
    // Add spin animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
    
    // Hide loader when page is loaded
    window.addEventListener('load', function() {
        setTimeout(() => {
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.remove();
            }, 500);
        }, 800);
    });
}

// ===============================================
// Enhanced Form Handling
// ===============================================

function initFormHandling() {
    const form = document.getElementById('contact-form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmission(this);
        });

        // Enhanced real-time validation
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
                
                // Add success state for valid fields
                if (validateField(this, true)) {
                    this.style.borderColor = '#10b981';
                    this.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.1)';
                }
            });

            // Enhanced focus effects
            input.addEventListener('focus', function() {
                this.style.transform = 'translateY(-2px)';
            });

            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.style.transform = 'translateY(0)';
                }
            });
        });

        // Enhanced phone number formatting
        const phoneInput = form.querySelector('#telefone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                this.value = formatPhoneNumber(this.value);
            });
        }
    }
}

function handleFormSubmission(form) {
    // Clear previous errors
    clearAllErrors(form);
    
    // Validate form
    const isValid = validateForm(form);
    
    if (isValid) {
        // Enhanced loading state
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        submitButton.innerHTML = `
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="
                    width: 16px;
                    height: 16px;
                    border: 2px solid rgba(255,255,255,0.3);
                    border-top: 2px solid white;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                "></div>
                Enviando...
            </div>
        `;
        submitButton.disabled = true;
        submitButton.style.transform = 'none';
        
        // Coletar dados do formulário
        const formData = new FormData(form);
        const data = {};
        
        // Converter FormData para objeto
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        // Enviar dados para o PHP
        fetch('send_email.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showSuccessMessage();
                form.reset();
                
                // Reset all field styles
                const inputs = form.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.style.borderColor = '';
                    input.style.boxShadow = '';
                    input.style.transform = '';
                });
            } else {
                showErrorMessage(result.message || 'Erro ao enviar formulário. Tente novamente.');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showErrorMessage('Erro de conexão. Verifique sua internet e tente novamente.');
        })
        .finally(() => {
            // Restaurar botão
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        });
    }
}

function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(field, silent = false) {
    const value = field.value.trim();
    let isValid = true;
    let message = '';
    
    // Check if required field is empty
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        message = 'Este campo é obrigatório';
    }
    
    // Specific validations
    if (value && field.type === 'email' && !isValidEmail(value)) {
        isValid = false;
        message = 'Por favor, insira um e-mail válido';
    }
    
    if (value && field.type === 'tel' && !isValidPhone(value)) {
        isValid = false;
        message = 'Por favor, insira um telefone válido';
    }
    
    if (!silent && !isValid) {
        showFieldError(field, message);
    }
    
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    field.style.borderColor = '#ef4444';
    field.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.style.cssText = `
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        animation: slideInUp 0.3s ease-out;
    `;
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
    
    field.style.borderColor = '';
    field.style.boxShadow = '';
}

function clearAllErrors(form) {
    const errorDivs = form.querySelectorAll('.field-error');
    errorDivs.forEach(div => div.remove());
    
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.style.borderColor = '';
        input.style.boxShadow = '';
    });
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^\(\d{2}\)\s\d{4,5}-\d{4}$/;
    return phoneRegex.test(phone);
}

function formatPhoneNumber(value) {
    // Remove all non-digits
    const digits = value.replace(/\D/g, '');
    
    // Format as (XX) XXXXX-XXXX or (XX) XXXX-XXXX
    if (digits.length <= 2) {
        return digits;
    } else if (digits.length <= 6) {
        return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
    } else if (digits.length <= 10) {
        return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
    } else {
        return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7, 11)}`;
    }
}

function showSuccessMessage() {
    // Create modal overlay
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        opacity: 0;
        transition: opacity 0.3s ease-out;
    `;
    
    // Create modal content
    const modal = document.createElement('div');
    modal.style.cssText = `
        background: white;
        padding: 3rem;
        border-radius: 1.5rem;
        text-align: center;
        max-width: 400px;
        margin: 0 1rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        transform: scale(0.9);
        transition: transform 0.3s ease-out;
    `;
    
    modal.innerHTML = `
        <div style="
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f1926e 0%, #e8744a 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
        ">
            <i class="fas fa-check"></i>
        </div>
        <h3 style="
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        ">Mensagem Enviada!</h3>
        <p style="
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        ">Obrigado pelo seu interesse! Entraremos em contato em breve.</p>
        <button onclick="closeSuccessModal()" style="
            background: linear-gradient(135deg, #f1926e 0%, #e8744a 100%);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 2rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease-out;
        " onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
            Fechar
        </button>
    `;
    
    overlay.appendChild(modal);
    document.body.appendChild(overlay);
    
    // Animate in
    setTimeout(() => {
        overlay.style.opacity = '1';
        modal.style.transform = 'scale(1)';
    }, 10);
    
    // Store reference for closing
    window.currentSuccessModal = overlay;
}

function closeSuccessModal() {
    const modal = window.currentSuccessModal;
    if (modal) {
        modal.style.opacity = '0';
        modal.querySelector('div').style.transform = 'scale(0.9)';
        setTimeout(() => {
            modal.remove();
            window.currentSuccessModal = null;
        }, 300);
    }
}

function showErrorMessage(message) {
    // Create modal overlay
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        opacity: 0;
        transition: opacity 0.3s ease-out;
    `;
    
    // Create modal content
    const modal = document.createElement('div');
    modal.style.cssText = `
        background: white;
        padding: 3rem;
        border-radius: 1.5rem;
        text-align: center;
        max-width: 400px;
        margin: 0 1rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        transform: scale(0.9);
        transition: transform 0.3s ease-out;
    `;
    
    modal.innerHTML = `
        <div style="
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
        ">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 style="
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        ">Erro no Envio</h3>
        <p style="
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        ">${message}</p>
        <button onclick="closeErrorModal()" style="
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 2rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease-out;
        " onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
            Tentar Novamente
        </button>
    `;
    
    overlay.appendChild(modal);
    document.body.appendChild(overlay);
    
    // Animate in
    setTimeout(() => {
        overlay.style.opacity = '1';
        modal.style.transform = 'scale(1)';
    }, 10);
    
    // Store reference for closing
    window.currentErrorModal = overlay;
}

function closeErrorModal() {
    const modal = window.currentErrorModal;
    if (modal) {
        modal.style.opacity = '0';
        modal.querySelector('div').style.transform = 'scale(0.9)';
        setTimeout(() => {
            modal.remove();
            window.currentErrorModal = null;
        }, 300);
    }
}

// ===============================================
// Enhanced Animations
// ===============================================

function initAnimations() {
    // Add entrance animations to hero elements
    const heroElements = document.querySelectorAll('.hero-badge, .hero-title, .hero-description, .hero-stats, .hero-actions');
    heroElements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            element.style.transition = 'all 0.6s ease-out';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, 200 + (index * 100));
    });
    
    // Add staggered animation to service features
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach(card => {
        const features = card.querySelectorAll('.service-features li');
        features.forEach((feature, index) => {
            feature.style.opacity = '0';
            feature.style.transform = 'translateX(-20px)';
            
            setTimeout(() => {
                feature.style.transition = 'all 0.4s ease-out';
                feature.style.opacity = '1';
                feature.style.transform = 'translateX(0)';
            }, index * 100);
        });
    });
    
    // Add counter animation to stats
    const statNumbers = document.querySelectorAll('.stat-number');
    statNumbers.forEach(stat => {
        const finalValue = stat.textContent;
        stat.textContent = '0';
        
        setTimeout(() => {
            animateCounter(stat, finalValue);
        }, 1000);
    });
}

function animateCounter(element, finalValue) {
    const duration = 2000;
    const startTime = performance.now();
    const isNumber = !isNaN(parseInt(finalValue));
    
    if (isNumber) {
        const finalNum = parseInt(finalValue.replace(/[^\d]/g, ''));
        
        function updateCounter(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const currentValue = Math.floor(progress * finalNum);
            element.textContent = finalValue.replace(/\d+/, currentValue);
            
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            }
        }
        
        requestAnimationFrame(updateCounter);
    } else {
        // For non-numeric values like "24/7"
        setTimeout(() => {
            element.textContent = finalValue;
        }, 500);
    }
}

// ===============================================
// Utility Functions
// ===============================================

function debounce(func, wait, immediate) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            timeout = null;
            if (!immediate) func(...args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func(...args);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// ===============================================
// Initialize on DOM ready
// ===============================================

// Add global styles for animations
const globalStyles = document.createElement('style');
globalStyles.textContent = `
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-on-scroll {
        transition: all 0.6s ease-out;
    }
    
    .fade-in {
        animation: fadeInUp 0.6s ease-out forwards;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(globalStyles);

// ===============================================
// Error Handling
// ===============================================

window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.error);
    // You could send this to an error reporting service
});

// ===============================================
// Performance Monitoring
// ===============================================

if ('performance' in window) {
    window.addEventListener('load', function() {
        setTimeout(() => {
            const perfData = performance.timing;
            const loadTime = perfData.loadEventEnd - perfData.navigationStart;
            console.log('Page load time:', loadTime + 'ms');
        }, 0);
    });
} 