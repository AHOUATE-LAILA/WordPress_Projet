jQuery(document).ready(function($) {
    
    // Gestion des onglets
    $('.tab-button, .menu-item').on('click', function() {
        var tabId = $(this).data('tab');
        
        // Mettre à jour les boutons et menu
        $('.tab-button').removeClass('active');
        $('.menu-item').removeClass('active');
        $(this).addClass('active');
        
        // Mettre à jour le contenu
        $('.tab-content').removeClass('active');
        $('#tab-' + tabId).addClass('active');
        
        // Charger les données selon l'onglet
        if (tabId === 'news') {
            loadNews();
        } else if (tabId === 'users') {
            loadUsers();
        } else if (tabId === 'stats') {
            loadStats();
        }
    });
    
    // Gestion du formulaire d'actualité
    $('#gaisio-news-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            title: $('#news-title').val(),
            content: $('#news-content').val(),
            image_url: $('#news-image').val(),
            status: $('#news-status').val()
        };
        
        // Validation
        if (!formData.title || !formData.content) {
            showMessage('Le titre et le contenu sont obligatoires', 'error');
            return;
        }
        
        // Vérifier si c'est une modification ou un ajout
        var editId = $('#gaisio-news-form').data('edit-id');
        var isEdit = editId ? true : false;
        
        // Ajouter l'ID si c'est une modification
        if (isEdit) {
            formData.news_id = editId;
        }
        
        $.ajax({
            url: gaisio_admin.ajax_url,
            type: 'POST',
            data: {
                action: isEdit ? 'gaisio_admin_update_news' : 'gaisio_admin_save_news',
                nonce: gaisio_admin.nonce,
                ...formData
            },
            beforeSend: function() {
                var submitBtn = $('button[type="submit"]');
                submitBtn.prop('disabled', true);
                submitBtn.text(isEdit ? 'Mise à jour...' : 'Enregistrement...');
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.data, 'success');
                    
                    // Réinitialiser le formulaire et le mode édition
                    $('#gaisio-news-form')[0].reset();
                    $('#gaisio-news-form').removeData('edit-id');
                    var submitBtn = $('button[type="submit"]');
                    submitBtn.removeClass('updating');
                    submitBtn.text('💾 Enregistrer l\'actualité');
                    $('#cancel-edit').hide();
                    
                    loadNews(); // Recharger la liste
                    
                    // Rafraîchir le carrousel d'actualités sur toutes les pages ouvertes
                    refreshNewsCarousel();
                    
                    // Notification spéciale si l'actualité est publiée
                    if (formData.status === 'published') {
                        var message = isEdit ? '✅ Actualité mise à jour avec succès !' : '✅ Actualité publiée avec succès !';
                        message += ' Elle apparaît maintenant sur la page d\'accueil.';
                        showMessage(message, 'success');
                        
                        // Ajouter un bouton pour ouvrir la page d'accueil
                        setTimeout(function() {
                            var openHomeButton = '<br><br><button onclick="window.open(\'' + window.location.origin + '\', \'_blank\')" class="gaisio-btn gaisio-btn-success">🌐 Ouvrir la page d\'accueil</button>';
                            $('.message.success').append(openHomeButton);
                        }, 1000);
                    }
                } else {
                    showMessage(response.data, 'error');
                }
            },
            error: function() {
                showMessage('Erreur lors de l\'enregistrement', 'error');
            },
            complete: function() {
                var submitBtn = $('button[type="submit"]');
                submitBtn.prop('disabled', false);
                if (!submitBtn.hasClass('updating')) {
                    submitBtn.text('💾 Enregistrer l\'actualité');
                } else {
                    submitBtn.text('💾 Mettre à jour l\'actualité');
                }
            }
        });
    });
    
    // Gestion du formulaire de création d'utilisateur
    $('#gaisio-create-user-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            email: $('#user-email').val(),
            display_name: $('#user-display-name').val()
        };
        
        // Validation
        if (!formData.email || !formData.display_name) {
            showMessage('L\'email et le nom d\'affichage sont obligatoires', 'error');
            return;
        }
        
        console.log('Envoi des données:', formData);
        console.log('URL AJAX:', gaisio_admin.ajax_url);
        console.log('Nonce:', gaisio_admin.nonce);
        
        $.ajax({
            url: gaisio_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'gaisio_admin_create_user',
                nonce: gaisio_admin.nonce,
                ...formData
            },
            beforeSend: function() {
                var submitBtn = $('#gaisio-create-user-form button[type="submit"]');
                submitBtn.prop('disabled', true);
                submitBtn.text('Création...');
            },
            success: function(response) {
                console.log('Réponse reçue:', response);
                if (response.success) {
                    // Afficher les informations de connexion
                    var userInfo = response.data.user_info;
                    var emailSent = response.data.email_sent;
                    var message = '✅ ' + response.data.message + '<br><br>';
                    message += '<div style="background: #e8f5e8; border: 1px solid #4caf50; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                    message += '<h4 style="margin: 0 0 10px 0; color: #2e7d32;">🔐 Informations de connexion générées automatiquement :</h4>';
                    message += '<p><strong>Identifiant de connexion :</strong> <code style="background: #fff; padding: 2px 4px; border-radius: 3px;">' + userInfo.username + '</code></p>';
                    message += '<p><strong>Email :</strong> ' + userInfo.email + '</p>';
                    message += '<p><strong>Code d\'accès :</strong> <code style="background: #fff; padding: 2px 4px; border-radius: 3px;">' + userInfo.access_code + '</code></p>';
                    
                    if (emailSent) {
                        message += '<p style="color: #2e7d32; font-weight: bold;">📧 Email envoyé automatiquement à l\'utilisateur</p>';
                    } else {
                        message += '<p style="color: #f57c00; font-weight: bold;">⚠️ Email non envoyé - Vérifiez la configuration email</p>';
                        message += '<p style="color: #d32f2f; font-weight: bold;">📧 Transmettez manuellement ces informations à l\'utilisateur</p>';
                    }
                    
                    message += '<p style="color: #d32f2f; font-weight: bold;">⚠️ Note : Ces informations sont également affichées pour l\'administrateur.</p>';
                    message += '</div>';
                    
                    // Ajouter le bouton de téléchargement PDF
                    if (response.data.download_button) {
                        message += '<div style="text-align: center; margin: 15px 0;">';
                        message += response.data.download_button;
                        message += '</div>';
                    }
                    
                    showMessage(message, 'success');
                    
                    // Réinitialiser le formulaire
                    $('#gaisio-create-user-form')[0].reset();
                    
                    // Recharger la liste des utilisateurs
                    loadUsers();
                } else {
                    showMessage(response.data, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.log('Erreur AJAX:', {xhr: xhr, status: status, error: error});
                showMessage('Erreur lors de la création de l\'utilisateur: ' + error, 'error');
            },
            complete: function() {
                var submitBtn = $('#gaisio-create-user-form button[type="submit"]');
                submitBtn.prop('disabled', false);
                submitBtn.text('👤 Créer l\'utilisateur');
            }
        });
    });
    
    // Nettoyer le formulaire au chargement de la page
    $(document).ready(function() {
        console.log('Document ready - Initialisation du formulaire');
        
        // Supprimer les champs non désirés du formulaire
        $('#user-username').closest('.form-group').remove();
        $('#user-role').closest('.form-group').remove();
        
        // Réorganiser le formulaire en une seule ligne
        $('.form-row').removeClass('form-row').addClass('form-group');
        
        // Validation initiale
        validateUserForm();
        
        console.log('Formulaire initialisé');
    });
    
    // Afficher la boîte d'information quand l'utilisateur commence à remplir le formulaire
    $('#user-email, #user-display-name').on('input', function() {
        var email = $('#user-email').val();
        var displayName = $('#user-display-name').val();
        
        if (email || displayName) {
            $('#user-creation-info').fadeIn(300);
        } else {
            $('#user-creation-info').fadeOut(300);
        }
        
        // Validation en temps réel
        validateUserForm();
    });
    
    // Fonction de validation en temps réel
    function validateUserForm() {
        var email = $('#user-email').val();
        var displayName = $('#user-display-name').val();
        var submitBtn = $('#gaisio-create-user-form button[type="submit"]');
        
        if (email && displayName) {
            submitBtn.prop('disabled', false).addClass('gaisio-btn-success');
        } else {
            submitBtn.prop('disabled', true).removeClass('gaisio-btn-success');
        }
    }
    
    // Fonction pour charger les actualités
    function loadNews() {
        $.ajax({
            url: gaisio_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'gaisio_admin_get_news',
                nonce: gaisio_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    displayNews(response.data);
                } else {
                    $('#gaisio-news-list').html('<div class="message error">' + response.data + '</div>');
                }
            },
            error: function() {
                $('#gaisio-news-list').html('<div class="message error">Erreur lors du chargement des actualités</div>');
            }
        });
    }
    
    // Fonction pour afficher les actualités
    function displayNews(news) {
        var html = '';
        
        if (news.length === 0) {
            html = '<div class="message info">Aucune actualité trouvée</div>';
        } else {
            html += '<div class="news-grid">';
            news.forEach(function(item) {
                var statusClass = item.status === 'published' ? 'published' : 'draft';
                var statusText = item.status === 'published' ? 'Publié' : 'Brouillon';
                var imageUrl = item.image_url || 'https://via.placeholder.com/300x200?text=Actualité';
                
                html += '<div class="news-card">';
                html += '<div class="news-card-image">';
                html += '<img src="' + imageUrl + '" alt="' + escapeHtml(item.title) + '">';
                html += '<div class="news-card-status ' + statusClass + '">' + statusText + '</div>';
                html += '</div>';
                html += '<div class="news-card-content">';
                html += '<h3 class="news-card-title">' + escapeHtml(item.title) + '</h3>';
                html += '<p class="news-card-excerpt">' + escapeHtml(item.content.substring(0, 100)) + (item.content.length > 100 ? '...' : '') + '</p>';
                html += '<div class="news-card-meta">';
                html += '<span class="news-card-date">📅 ' + formatDate(item.created_at) + '</span>';
                html += '</div>';
                html += '<div class="news-card-actions">';
                html += '<button class="gaisio-btn gaisio-btn-primary edit-news" data-id="' + item.id + '" data-title="' + escapeHtml(item.title) + '" data-content="' + escapeHtml(item.content) + '" data-image="' + escapeHtml(item.image_url || '') + '" data-status="' + item.status + '">✏️ Modifier</button>';
                html += '<button class="gaisio-btn gaisio-btn-danger delete-news" data-id="' + item.id + '">🗑️ Supprimer</button>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            });
            html += '</div>';
        }
        
        $('#gaisio-news-list').html(html);
    }
    
    // Fonction pour charger les utilisateurs
    function loadUsers() {
        $.ajax({
            url: gaisio_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'gaisio_admin_get_users',
                nonce: gaisio_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    displayUsers(response.data);
                } else {
                    $('#gaisio-users-list').html('<div class="message error">' + response.data + '</div>');
                }
            },
            error: function() {
                $('#gaisio-users-list').html('<div class="message error">Erreur lors du chargement des utilisateurs</div>');
            }
        });
    }
    
    // Fonction pour afficher les utilisateurs
    function displayUsers(users) {
        var html = '';
        
        if (users.length === 0) {
            html = '<div class="message info">Aucun utilisateur trouvé</div>';
        } else {
            html += '<div class="users-grid">';
            users.forEach(function(user) {
                html += '<div class="user-card">';
                html += '<div class="user-card-header">';
                html += '<h3 class="user-card-name">' + escapeHtml(user.display_name || user.username) + '</h3>';
                html += '</div>';
                
                html += '<div class="user-card-content">';
                html += '<div class="user-info">';
                html += '<p><strong>📧 Email :</strong> ' + escapeHtml(user.user_email || user.email) + '</p>';
                html += '<p><strong>👤 Nom d\'utilisateur :</strong> ' + escapeHtml(user.username) + '</p>';
                html += '<p><strong>📅 Inscrit le :</strong> ' + formatDate(user.created_at) + '</p>';
                html += '</div>';
                
                html += '<div class="user-card-actions">';
                html += '<button class="gaisio-btn gaisio-btn-download" onclick="downloadUserCredentials(' + user.user_id + ')">📄 Télécharger</button>';

                html += '<button class="gaisio-btn gaisio-btn-danger delete-user" data-id="' + user.user_id + '" data-username="' + escapeHtml(user.username) + '">🗑️ Supprimer</button>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            });
            html += '</div>';
        }
        
        $('#gaisio-users-list').html(html);
    }
    
    // Fonction pour charger les statistiques
    function loadStats() {
        $.ajax({
            url: gaisio_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'gaisio_admin_get_stats',
                nonce: gaisio_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    displayStats(response.data);
                } else {
                    $('#gaisio-stats-display').html('<div class="message error">' + response.data + '</div>');
                }
            },
            error: function() {
                $('#gaisio-stats-display').html('<div class="message error">Erreur lors du chargement des statistiques</div>');
            }
        });
    }
    
    // Fonction pour afficher les statistiques
    function displayStats(stats) {
        var html = '<div class="stats-grid">';
        
        // Statistiques principales
        html += '<div class="stat-card primary">';
        html += '<div class="stat-icon">🌍</div>';
        html += '<h3>' + stats.total_earthquakes + '</h3>';
        html += '<p>Tremblements de terre enregistrés</p>';
        html += '</div>';
        
        html += '<div class="stat-card primary">';
        html += '<div class="stat-icon">👥</div>';
        html += '<h3>' + stats.total_users + '</h3>';
        html += '<p>Utilisateurs inscrits</p>';
        html += '</div>';
        
        html += '<div class="stat-card primary">';
        html += '<div class="stat-icon">📊</div>';
        html += '<h3>' + stats.total_signalements + '</h3>';
        html += '<p>Signalements reçus</p>';
        html += '</div>';
        
        html += '<div class="stat-card primary">';
        html += '<div class="stat-icon">⚡</div>';
        html += '<h3>' + stats.latest_magnitude + '</h3>';
        html += '<p>Magnitude la plus élevée</p>';
        html += '</div>';
        
        html += '</div>';
        
        // Informations détaillées
        html += '<div class="stats-details">';
        html += '<h3>📈 Informations détaillées</h3>';
        html += '<div class="details-grid">';
        
        html += '<div class="detail-item">';
        html += '<strong>Dernier tremblement de terre :</strong>';
        html += '<span>' + stats.latest_earthquake_date + '</span>';
        html += '</div>';
        
        html += '<div class="detail-item">';
        html += '<strong>Dernier utilisateur inscrit :</strong>';
        html += '<span>' + stats.latest_user_date + '</span>';
        html += '</div>';
        
        html += '<div class="detail-item">';
        html += '<strong>Statut de la plateforme :</strong>';
        html += '<span class="status-active">' + stats.platform_status + '</span>';
        html += '</div>';
        
        html += '</div>';
        html += '</div>';
        
        $('#gaisio-stats-display').html(html);
    }
    
    // Gestion de la modification d'actualité
    $(document).on('click', '.edit-news', function() {
        var newsId = $(this).data('id');
        var title = $(this).data('title');
        var content = $(this).data('content');
        var image = $(this).data('image');
        var status = $(this).data('status');
        
        // Remplir le formulaire avec les données existantes
        $('#news-title').val(title);
        $('#news-content').val(content);
        $('#news-image').val(image);
        $('#news-status').val(status);
        
        // Changer le bouton de soumission pour indiquer qu'il s'agit d'une modification
        var submitBtn = $('#gaisio-news-form button[type="submit"]');
        submitBtn.text('💾 Mettre à jour l\'actualité');
        submitBtn.addClass('updating');
        
        // Afficher le bouton d'annulation
        $('#cancel-edit').show();
        
        // Ajouter l'ID de l'actualité au formulaire pour la modification
        $('#gaisio-news-form').data('edit-id', newsId);
        
        // Faire défiler vers le formulaire
        $('html, body').animate({
            scrollTop: $('#gaisio-news-form').offset().top - 50
        }, 500);
        
        showMessage('📝 Mode édition activé. Modifiez les champs et cliquez sur "Mettre à jour"', 'info');
    });
    
    // Gestion de la suppression d'actualité
    $(document).on('click', '.delete-news', function() {
        var newsId = $(this).data('id');
        
        if (confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?')) {
            $.ajax({
                url: gaisio_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'gaisio_admin_delete_news',
                    nonce: gaisio_admin.nonce,
                    news_id: newsId
                },
                success: function(response) {
                    if (response.success) {
                        showMessage(response.data, 'success');
                        loadNews(); // Recharger la liste
                        
                        // Rafraîchir aussi le carrousel d'actualités
                        refreshNewsCarousel();
                    } else {
                        showMessage(response.data, 'error');
                    }
                },
                error: function() {
                    showMessage('Erreur lors de la suppression', 'error');
                }
            });
        }
    });
    
    // Gestion de la suppression d'utilisateur
    $(document).on('click', '.delete-user', function() {
        var userId = $(this).data('id');
        var username = $(this).data('username');
        
        if (confirm('Êtes-vous sûr de vouloir supprimer l\'utilisateur "' + username + '" ?')) {
            $.ajax({
                url: gaisio_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'gaisio_admin_delete_user',
                    nonce: gaisio_admin.nonce,
                    user_id: userId
                },
                success: function(response) {
                    if (response.success) {
                        showMessage(response.data, 'success');
                        loadUsers(); // Recharger la liste
                    } else {
                        showMessage(response.data, 'error');
                    }
                },
                error: function() {
                    showMessage('Erreur lors de la suppression', 'error');
                }
            });
        }
    });
    
    // Fonction pour afficher les messages
    function showMessage(message, type) {
        var messageHtml = '<div class="message ' + type + '">' + message + '</div>';
        
        // Supprimer les anciens messages
        $('.message').remove();
        
        // Ajouter le nouveau message en haut de la page
        $('.wrap').prepend(messageHtml);
        
        // Faire défiler vers le haut
        $('html, body').animate({ scrollTop: 0 }, 500);
        
        // Supprimer le message après 5 secondes
        setTimeout(function() {
            $('.message').fadeOut(500, function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    // Fonction pour échapper le HTML
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Fonction pour formater la date
    function formatDate(dateString) {
        var date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    // Fonction pour rafraîchir le carrousel d'actualités sur toutes les pages
    function refreshNewsCarousel() {
        console.log('🔄 Tentative de rafraîchissement du carrousel d\'actualités...');
        
        // Méthode 1: Rafraîchir si le carrousel est présent sur cette page
        if ($('.gaisio-news-carousel').length > 0) {
            console.log('✅ Carrousel trouvé sur cette page, rafraîchissement local...');
            
            // Recharger les actualités via AJAX
            $.ajax({
                url: gaisio_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gaisio_get_news_frontend',
                    nonce: gaisio_ajax.nonce
                },
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        console.log('✅ Actualités mises à jour:', response.data.length);
                        displayNewsCarousel(response.data);
                        // Réinitialiser le carrousel si nécessaire
                        if (typeof initCarousel === 'function') {
                            initCarousel();
                        }
                    } else {
                        console.log('❌ Aucune actualité trouvée après mise à jour');
                        $('#gaisio-news-section').closest('.gaisio-public-section').hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.log('❌ Erreur lors du rafraîchissement:', error);
                }
            });
        } else {
            console.log('ℹ️ Aucun carrousel sur cette page, notification envoyée...');
            
            // Méthode 2: Envoyer une notification pour forcer le rafraîchissement
            // sur d'autres onglets/fenêtres ouvertes
            if (typeof window.postMessage === 'function') {
                window.postMessage({
                    type: 'gaisio_news_updated',
                    message: 'Une nouvelle actualité a été ajoutée'
                }, '*');
            }
            
            // Méthode 3: Afficher un message à l'admin
            showMessage('✅ Actualité enregistrée ! Pour voir les changements sur la page d\'accueil, rechargez la page ou ouvrez un nouvel onglet.', 'success');
        }
    }
    
    // Fonction pour afficher les actualités dans le carrousel (copie de gaisio-earthquake.js)
    function displayNewsCarousel(news) {
        var html = '';
        
        news.forEach(function(item, index) {
            var imageUrl = item.image_url || 'https://via.placeholder.com/400x200?text=Actualité';
            
            html += '<div class="carousel-slide' + (index === 0 ? ' active' : '') + '">';
            html += '<div class="slide-image">';
            html += '<img src="' + imageUrl + '" alt="' + escapeHtml(item.title) + '">';
            html += '</div>';
            html += '<div class="slide-content">';
            html += '<h3>' + escapeHtml(item.title) + '</h3>';
            html += '<p>' + escapeHtml(item.content) + '</p>';
            html += '<small>' + formatDate(item.created_at) + '</small>';
            html += '</div>';
            html += '</div>';
        });
        
        $('#gaisio-news-slides').html(html);
    }
    
    // Ajouter des boutons d'action
    $('<button type="button" id="refresh-carousel" class="gaisio-btn gaisio-btn-secondary" style="margin-left: 10px;">🔄 Rafraîchir le carrousel</button>').insertAfter('#gaisio-news-form button[type="submit"]');
    $('<button type="button" id="cancel-edit" class="gaisio-btn gaisio-btn-secondary" style="margin-left: 10px; display: none;">❌ Annuler l\'édition</button>').insertAfter('#refresh-carousel');
    
    // Gestion du bouton de rafraîchissement
    $(document).on('click', '#refresh-carousel', function() {
        showMessage('🔄 Rafraîchissement du carrousel en cours...', 'info');
        
        // Envoyer une notification pour forcer le rafraîchissement
        if (typeof window.postMessage === 'function') {
            window.postMessage({
                type: 'gaisio_news_updated',
                message: 'Rafraîchissement manuel demandé'
            }, '*');
        }
        
        // Afficher un message avec un lien vers la page d'accueil
        setTimeout(function() {
            showMessage('✅ Rafraîchissement envoyé ! Si vous avez la page d\'accueil ouverte, elle devrait se mettre à jour automatiquement. <br><br><a href="' + window.location.origin + '" target="_blank" class="gaisio-btn gaisio-btn-success">🌐 Ouvrir la page d\'accueil</a>', 'success');
        }, 1000);
    });
    
    // Gestion du bouton d'annulation d'édition
    $(document).on('click', '#cancel-edit', function() {
        // Réinitialiser le formulaire
        $('#gaisio-news-form')[0].reset();
        $('#gaisio-news-form').removeData('edit-id');
        
        // Remettre le bouton en mode ajout
        var submitBtn = $('button[type="submit"]');
        submitBtn.removeClass('updating');
        submitBtn.text('💾 Enregistrer l\'actualité');
        
        // Masquer le bouton d'annulation
        $('#cancel-edit').hide();
        
        showMessage('❌ Mode édition annulé. Vous pouvez maintenant ajouter une nouvelle actualité.', 'info');
    });
    
    // Gestion du téléchargement PDF des informations de connexion
    $(document).on('click', '.download-pdf', function() {
        var userId = $(this).data('id');
        var username = $(this).data('username');
        
        // Désactiver le bouton pendant le téléchargement
        var button = $(this);
        button.prop('disabled', true);
        button.text('⏳ Génération...');
        
        // Créer un formulaire temporaire pour le téléchargement
        var form = $('<form>', {
            'method': 'POST',
            'action': gaisio_admin.ajax_url,
            'target': '_blank'
        });
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'action',
            'value': 'gaisio_download_user_credentials_pdf'
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'nonce',
            'value': gaisio_admin.nonce
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'user_id',
            'value': userId
        }));
        
        // Ajouter le formulaire au DOM et le soumettre
        $('body').append(form);
        form.submit();
        form.remove();
        
        // Réactiver le bouton après un délai
        setTimeout(function() {
            button.prop('disabled', false);
            button.text('📄 Télécharger');
        }, 2000);
        
        showMessage('📄 Génération du document en cours... Le téléchargement devrait commencer automatiquement.', 'info');
    });
    
    // Fonction globale pour télécharger les informations de connexion (utilisée dans la création d'utilisateur)
    window.downloadUserCredentials = function(userId) {
        // Créer un formulaire temporaire pour le téléchargement
        var form = $('<form>', {
            'method': 'POST',
            'action': gaisio_admin.ajax_url,
            'target': '_blank'
        });
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'action',
            'value': 'gaisio_download_user_credentials_pdf'
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'nonce',
            'value': gaisio_admin.nonce
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'user_id',
            'value': userId
        }));
        
        // Ajouter le formulaire au DOM et le soumettre
        $('body').append(form);
        form.submit();
        form.remove();
        
        showMessage('📄 Téléchargement du document en cours...', 'info');
    };
    
    // Charger les données initiales
    loadNews();
    
}); 