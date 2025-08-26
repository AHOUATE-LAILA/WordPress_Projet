jQuery(document).ready(function($) {
    // Gestion du formulaire de connexion administrateur
    $('#gaisio-admin-login-form').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $(this).find('button[type="submit"]');
        const btnText = submitBtn.find('.btn-text');
        const btnLoading = submitBtn.find('.btn-loading');
        btnText.hide();
        btnLoading.show();
        submitBtn.prop('disabled', true);

        const formData = {
            action: 'gaisio_admin_login',
            nonce: $('input[name="admin_nonce_field"]').val() || gaisio_user.nonce,
            username: $('#admin-username').val(),
            password: $('#admin-password').val(),
            remember: $('#admin-remember').is(':checked')
        };

        $.ajax({
            url: gaisio_user.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    window.location.href = response.data.redirect_url;
                } else {
                    alert(response.data || 'Erreur de connexion admin');
                }
            },
            error: function() {
                alert('Erreur de réseau');
            },
            complete: function() {
                btnLoading.hide();
                btnText.show();
                submitBtn.prop('disabled', false);
            }
        });
    });
    'use strict';
    
    // Gestion du bouton de signalement
    $('#signalement-button').on('click', function() {
        $(this).hide();
        $('#signalement-form-container').show();
        
        // Faire défiler vers le formulaire
        $('#signalement-form-container').get(0).scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    });
    
    // Gestion du formulaire de signalement
    $('#gaisio-signalement-form').on('submit', function(e) {
        e.preventDefault();
        
        // Afficher le spinner
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<span class="spinner"></span> Envoi en cours...');
        submitBtn.prop('disabled', true);
        
        // Récupérer les données du formulaire
        const formData = new FormData(this);
        formData.append('action', 'gaisio_submit_signalement');
        formData.append('nonce', gaisio_public.nonce);
        
        // Envoyer la requête AJAX
        $.ajax({
            url: gaisio_public.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Succès
                    showMessage('success', response.data.message);
                    
                    // Réinitialiser le formulaire
                    $('#gaisio-signalement-form')[0].reset();
                    
                    // Masquer le formulaire et afficher le bouton
                    $('#signalement-form-container').hide();
                    $('#signalement-button').show();
                    
                    // Faire défiler vers le message
                    $('html, body').animate({
                        scrollTop: $('#signalement-message').offset().top - 100
                    }, 500);
                } else {
                    // Erreur
                    showMessage('error', response.data || 'Erreur lors de l\'envoi du signalement');
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Erreur de connexion';
                
                if (xhr.status === 400) {
                    errorMessage = 'Données invalides';
                } else if (xhr.status === 500) {
                    errorMessage = 'Erreur serveur';
                } else if (xhr.status === 0) {
                    errorMessage = 'Problème de réseau';
                }
                
                showMessage('error', errorMessage);
            },
            complete: function() {
                // Restaurer le bouton
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });
    
    // Gestion du formulaire de connexion utilisateur
    $('#gaisio-user-login-form').on('submit', function(e) {
        console.log('Formulaire utilisateur soumis !'); // Debug
        e.preventDefault();
        
        // Vérifier que les variables AJAX sont disponibles
        if (typeof gaisio_user === 'undefined') {
            console.error('gaisio_user non défini');
            alert('Erreur de configuration AJAX. Veuillez rafraîchir la page.');
            return;
        }
        
        console.log('gaisio_user:', gaisio_user); // Debug
        
        // Afficher le spinner
        const submitBtn = $(this).find('button[type="submit"]');
        const btnText = submitBtn.find('.btn-text');
        const btnLoading = submitBtn.find('.btn-loading');
        
        console.log('SubmitBtn:', submitBtn.length); // Debug
        console.log('BtnText:', btnText.length); // Debug
        console.log('BtnLoading:', btnLoading.length); // Debug
        
        btnText.hide();
        btnLoading.show();
        submitBtn.prop('disabled', true);
        
        // Récupérer les données du formulaire
        const formData = {
            action: 'gaisio_user_login',
            nonce: gaisio_user.nonce,
            username: $('#login-username').val(),
            access_code: $('#login-access-code').val(),
            remember: $('#login-remember').is(':checked')
        };
        
        console.log('Données envoyées:', formData); // Debug
        
        // Envoyer la requête AJAX
        $.ajax({
            url: gaisio_user.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                console.log('Réponse reçue:', response); // Debug
                
                if (response.success) {
                    // Succès
                    showLoginMessage('success', response.data.message);
                    
                    console.log('Redirection vers:', response.data.redirect_url); // Debug
                    
                    // Rediriger après un délai
                    setTimeout(function() {
                        window.location.href = response.data.redirect_url;
                    }, 2000);
                } else {
                    // Erreur
                    showLoginMessage('error', response.data || 'Erreur lors de la connexion');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur AJAX:', {xhr: xhr, status: status, error: error}); // Debug
                
                let errorMessage = 'Erreur de connexion';
                
                if (xhr.status === 400) {
                    errorMessage = 'Données invalides';
                } else if (xhr.status === 500) {
                    errorMessage = 'Erreur serveur';
                } else if (xhr.status === 0) {
                    errorMessage = 'Problème de réseau';
                }
                
                showLoginMessage('error', errorMessage);
            },
            complete: function() {
                // Restaurer le bouton
                btnLoading.hide();
                btnText.show();
                submitBtn.prop('disabled', false);
            }
        });
    });
    
    // Fonction pour afficher les messages de signalement
    function showMessage(type, message) {
        const messageContainer = $('#signalement-message');
        messageContainer.removeClass('success error').addClass(type);
        messageContainer.html(message).show();
        
        // Faire défiler vers le message
        $('html, body').animate({
            scrollTop: messageContainer.offset().top - 100
        }, 500);
        
        // Masquer le message après 5 secondes
        setTimeout(function() {
            messageContainer.fadeOut();
        }, 5000);
    }
    
    // Fonction pour afficher les messages de connexion
    function showLoginMessage(type, message) {
        const messageContainer = $('#login-message');
        messageContainer.removeClass('success error').addClass(type);
        messageContainer.html(message).show();
        
        // Faire défiler vers le message
        $('html, body').animate({
            scrollTop: messageContainer.offset().top - 100
        }, 500);
        
        // Masquer le message après 5 secondes
        setTimeout(function() {
            messageContainer.fadeOut();
        }, 5000);
    }
}); 