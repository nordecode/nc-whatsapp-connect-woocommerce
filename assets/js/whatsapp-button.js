(function($) {
    'use strict';
    
    function getMessage() {
        const vars = window.whatsappConnectVars;
        
        if (vars.isProduct && vars.productData) {
            try {
                return vars.productMessage
                    .replace(/{product_name}/g, vars.productData.name || '')
                    .replace(/{product_price}/g, vars.productData.price || '')
                    .replace(/{product_url}/g, vars.productData.url || '');
            } catch (e) {
                console.error('NC WhatsApp Connect - Erro ao formatar mensagem do produto:', e);
                return vars.message;
            }
        }
        
        return vars.message;
    }

    function isMobile() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    function getWhatsAppUrl(phone, message) {
        const baseUrl = isMobile() ? 'whatsapp://' : 'https://web.whatsapp.com/';
        return `${baseUrl}send?phone=${phone}&text=${message}`;
    }

    function openWhatsApp(url) {
        if (isMobile()) {
            window.location.href = url;
        } else {
            const newWindow = window.open();
            if (newWindow) {
                newWindow.opener = null;
                newWindow.location = url;
                newWindow.target = "_blank";
                newWindow.rel = "noopener noreferrer";
            }
        }
    }

    function initWhatsApp() {
        // Preenche a mensagem inicial
        $('#whatsapp-message').val(getMessage());

        // Manipuladores de eventos
        $('.nc-whatsapp-btn').click(function(e) {
            if (!$(e.target).closest('.nc-popup-content').length && 
                !$(e.target).closest('.nc-close-popup').length) {
                $(this).toggleClass('active');
            }
        });

        $('.nc-close-popup').click(function(e) {
            e.stopPropagation();
            $('.nc-whatsapp-btn').removeClass('active');
        });

        $('.nc-send-message').click(function() {
            const customMessage = $('#whatsapp-message').val().trim();
            if (customMessage) {
                const message = encodeURIComponent(customMessage);
                const url = getWhatsAppUrl(window.whatsappConnectVars.phone, message);
                
                openWhatsApp(url);
                $('.nc-whatsapp-btn').removeClass('active');
            }
        });
    }

    $(document).ready(initWhatsApp);
})(jQuery);