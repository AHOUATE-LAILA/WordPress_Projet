/**
 * Gaisio Admin Frontend Scripts
 * Gestion de l'interactivité de l'interface d'administration complète sur le frontend
 */

jQuery(document).ready(function($) {
    
    // Initialiser l'interface d'administration
    if ($('.gaisio-admin-frontend-wrap').length > 0) {
        initAdminInterface();
    }
    
    /**
     * Initialiser l'interface d'administration
     */
    function initAdminInterface() {
        // Charger le contenu initial selon l'onglet actif
        var activeTab = $('.gaisio-nav-tab-active').attr('href').substring(1);
        loadTabContent(activeTab);
        
        // Gestion des onglets
        initTabs();
        
        // Gestion des formulaires
        initForms();
    }
    
    /**
     * Initialiser la navigation par onglets
     */
    function initTabs() {
        $('.gaisio-nav-tab').on('click', function(e) {
            e.preventDefault();
            
            var targetTab = $(this).attr('href').substring(1);
            
            // Mettre à jour les onglets actifs
            $('.gaisio-nav-tab').removeClass('gaisio-nav-tab-active');
            $(this).addClass('gaisio-nav-tab-active');
            
            // Afficher le contenu de l'onglet
            $('.gaisio-tab-content').removeClass('active');
            $('#' + targetTab).addClass('active');
            
            // Charger le contenu de l'onglet
            loadTabContent(targetTab);
        });
    }
    
    /**
     * Charger le contenu d'un onglet
     */
    function loadTabContent(tabName) {
        switch(tabName) {
            case 'actualites':
                loadNews();
                break;
            case 'utilisateurs':
                loadUsers();
                break;
            case 'statistiques':
                loadStats();
                break;
        }
    }
    
    /**
     * Initialiser les formulaires
     */
    function initForms() {
        // Formulaire d'ajout d'actualité
        $('#gaisio-news-form').on('submit', function(e) {
            e.preventDefault();
            saveNews();
        });
    }
    
    /**
     * Charger les actualités depuis l'API
     */
    function loadNews() {
        var container = $('#gaisio-news-list');
        
        // Afficher le chargement
        container.html('<div class="gaisio-loading">Chargement des actualités...</div>');
        
        $.ajax({
            url: gaisioFrontend.ajax_url,
            type: 'POST',
            data: {
                action: 'gaisio_get_news',
                nonce: gaisioFrontend.nonce
            },
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    displayNews(response.data);
                } else {
                    container.html('<div class="gaisio-no-news">Aucune actualité trouvée.</div>');
                }
            },
            error: function() {
                container.html('<div class="gaisio-error">Erreur lors du chargement des actualités.</div>');
            }
        });
    }
    
    /**
     * Sauvegarder une actualité
     */
    function saveNews() {
        var form = $('#gaisio-news-form');
        var submitBtn = form.find('button[type="submit"]');
        var btnText = submitBtn.find('.btn-text');
        var btnLoading = submitBtn.find('.btn-loading');
        
        // Afficher l'état de chargement
        btnText.hide();
        btnLoading.show();
        submitBtn.prop('disabled', true);
        
        var formData = {
            action: 'gaisio_save_news',
            nonce: gaisioFrontend.nonce,
            title: $('#news-title').val(),
            description: $('#news-description').val(),
            image_url: $('#news-image').val()
        };
        
        $.ajax({
            url: gaisioFrontend.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Réinitialiser le formulaire
                    form[0].reset();
                    
                    // Recharger la liste des actualités
                    loadNews();
                    
                    // Afficher un message de succès
                    showMessage('Actualité ajoutée avec succès !', 'success');
                } else {
                    showMessage('Erreur lors de l\'ajout de l\'actualité : ' + response.data, 'error');
                }
            },
            error: function() {
                showMessage('Erreur de connexion lors de l\'ajout de l\'actualité.', 'error');
            },
            complete: function() {
                // Restaurer l'état du bouton
                btnText.show();
                btnLoading.hide();
                submitBtn.prop('disabled', false);
            }
        });
    }
    
    /**
     * Charger les utilisateurs
     */
    function loadUsers() {
        var container = $('#gaisio-users-list');
        container.html('<div class="gaisio-loading">Chargement des utilisateurs...</div>');
        
        // Placeholder pour le chargement des utilisateurs
        setTimeout(function() {
            container.html('<div class="gaisio-no-users">Fonctionnalité de gestion des utilisateurs en cours de développement.</div>');
        }, 1000);
    }
    
    /**
     * Charger les statistiques
     */
    function loadStats() {
        var container = $('#gaisio-stats-grid');
        container.html('<div class="gaisio-loading">Chargement des statistiques...</div>');
        
        // Placeholder pour le chargement des statistiques
        setTimeout(function() {
            container.html('<div class="gaisio-no-stats">Fonctionnalité de statistiques en cours de développement.</div>');
        }, 1000);
    }
    
    /**
     * Afficher les actualités dans le conteneur
     */
    function displayNews(news) {
        var container = $('#gaisio-news-list');
        var html = '';
        
        news.forEach(function(item) {
            html += createNewsItemHTML(item);
        });
        
        container.html(html);
    }
    
    /**
     * Créer le HTML pour un élément d'actualité
     */
    function createNewsItemHTML(news) {
        var date = news.date ? formatDate(news.date) : '';
        
        return `
            <div class="gaisio-news-item" data-news-id="${news.id}">
                <img src="${news.image_url}" alt="${news.title}" class="gaisio-news-image" onerror="this.src='${gaisioFrontend.plugin_url}images/default-news.jpg'">
                <div class="gaisio-news-content">
                    <h3 class="gaisio-news-title">${news.title}</h3>
                    <p class="gaisio-news-description">${news.description || ''}</p>
                    ${date ? '<p class="gaisio-news-date">' + date + '</p>' : ''}
                </div>
                <div class="gaisio-news-actions">
                    <button class="gaisio-btn gaisio-btn-danger" onclick="deleteNews(${news.id})">
                        🗑️ Supprimer
                    </button>
                </div>
            </div>
        `;
    }
    
    /**
     * Supprimer une actualité
     */
    window.deleteNews = function(newsId) {
        if (confirm(gaisioFrontend.strings.confirm_delete)) {
            $.ajax({
                url: gaisioFrontend.ajax_url,
                type: 'POST',
                data: {
                    action: 'gaisio_delete_news',
                    nonce: gaisioFrontend.nonce,
                    news_id: newsId
                },
                success: function(response) {
                    if (response.success) {
                        loadNews();
                        showMessage('Actualité supprimée avec succès !', 'success');
                    } else {
                        showMessage('Erreur lors de la suppression : ' + response.data, 'error');
                    }
                },
                error: function() {
                    showMessage('Erreur de connexion lors de la suppression.', 'error');
                }
            });
        }
    };
    
    /**
     * Formater une date
     */
    function formatDate(dateString) {
        if (!dateString) return '';
        
        var date = new Date(dateString);
        if (isNaN(date.getTime())) return '';
        
        var options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        
        return date.toLocaleDateString('fr-FR', options);
    }
    
    /**
     * Afficher un message
     */
    function showMessage(message, type) {
        var messageClass = type === 'success' ? 'gaisio-message-success' : 'gaisio-message-error';
        var messageHtml = '<div class="gaisio-message ' + messageClass + '">' + message + '</div>';
        
        // Supprimer les anciens messages
        $('.gaisio-message').remove();
        
        // Ajouter le nouveau message
        $('.gaisio-admin-frontend-wrap').prepend(messageHtml);
        
        // Supprimer le message après 5 secondes
        setTimeout(function() {
            $('.gaisio-message').fadeOut(500, function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    /**
     * Gestion des erreurs d'images
     */
    $(document).on('error', '.gaisio-news-image', function() {
        var defaultImage = gaisioFrontend.plugin_url + 'images/default-news.jpg';
        if (this.src !== defaultImage) {
            this.src = defaultImage;
        }
    });
    
    /**
     * Animation d'apparition des éléments
     */
    $(document).on('DOMNodeInserted', '.gaisio-news-item', function() {
        $(this).hide().fadeIn(500);
    });
    
    /**
     * Gestion du responsive et de l'accessibilité
     */
    $(window).on('resize', function() {
        // Ajuster la taille des images si nécessaire
        $('.gaisio-news-image').each(function() {
            var container = $(this).closest('.gaisio-news-item');
            var isMobile = container.width() < 768;
            
            if (isMobile) {
                $(this).css({
                    'width': '100px',
                    'height': '100px',
                    'margin': '0 0 15px 0'
                });
            } else {
                $(this).css({
                    'width': '80px',
                    'height': '80px',
                    'margin': '0 20px 0 0'
                });
            }
        });
    });
    
    /**
     * Initialiser les ajustements de taille
     */
    $(window).trigger('resize');
    
}); 