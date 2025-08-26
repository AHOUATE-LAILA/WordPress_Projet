/**
 * Gaisio Earthquake Manager - Admin Scripts
 * Gestion de l'interactivité de la page d'administration
 */

jQuery(document).ready(function($) {
    
    // ========================================
    // Navigation par Onglets
    // ========================================
    
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        
        var targetTab = $(this).attr('href');
        
        // Mettre à jour les onglets actifs
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        // Afficher le contenu de l'onglet
        $('.tab-content').removeClass('active');
        $(targetTab).addClass('active');
        
        // Charger les données de l'onglet si nécessaire
        if (targetTab === '#actualites') {
            loadNews();
        } else if (targetTab === '#utilisateurs') {
            loadUsers();
        } else if (targetTab === '#statistiques') {
            loadStats();
        }
    });
    
    // ========================================
    // Gestion des Actualités
    // ========================================
    
    // Formulaire d'ajout/modification d'actualité
    $('#gaisio-news-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'gaisio_save_news',
            nonce: gaisioAdmin.nonce,
            title: $('#news-title').val(),
            description: $('#news-description').val(),
            image_url: $('#news-image').val(),
            pub_date: $('#news-date').val(),
            status: $('#news-status').val(),
            display_order: $('#news-order').val()
        };
        
        // Validation
        if (!formData.title || !formData.image_url) {
            showMessage('Veuillez remplir tous les champs obligatoires.', 'error');
            return;
        }
        
        // Envoyer la requête
        saveNews(formData);
    });
    
    // Réinitialiser le formulaire
    $('#reset-form').on('click', function() {
        $('#gaisio-news-form')[0].reset();
        $('#news-date').val(new Date().toISOString().split('T')[0]);
        $('#news-order').val('0');
    });
    
    // Charger les actualités existantes
    function loadNews() {
        $.ajax({
            url: gaisioAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'gaisio_get_news',
                nonce: gaisioAdmin.nonce
            },
            beforeSend: function() {
                $('#news-table-body').html('<tr><td colspan="7" class="loading">Chargement des actualités...</td></tr>');
            },
            success: function(response) {
                if (response.success) {
                    displayNews(response.data);
                } else {
                    $('#news-table-body').html('<tr><td colspan="7">Aucune actualité trouvée.</td></tr>');
                }
            },
            error: function() {
                $('#news-table-body').html('<tr><td colspan="7">Erreur lors du chargement des actualités.</td></tr>');
            }
        });
    }
    
    // Afficher les actualités dans le tableau
    function displayNews(news) {
        var html = '';
        
        if (Object.keys(news).length === 0) {
            html = '<tr><td colspan="7">Aucune actualité trouvée.</td></tr>';
        } else {
            // Trier par ordre d'affichage
            var sortedNews = Object.entries(news).sort((a, b) => a[1].display_order - b[1].display_order);
            
            sortedNews.forEach(function([id, item]) {
                html += '<tr>';
                html += '<td><img src="' + item.image_url + '" alt="' + item.title + '" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;"></td>';
                html += '<td><strong>' + item.title + '</strong></td>';
                html += '<td>' + (item.description || '-') + '</td>';
                html += '<td>' + formatDate(item.pub_date) + '</td>';
                html += '<td><span class="status-badge status-' + item.status + '">' + getStatusLabel(item.status) + '</span></td>';
                html += '<td>' + item.display_order + '</td>';
                html += '<td class="action-buttons">';
                html += '<button class="action-btn edit" data-id="' + id + '">✏️ Modifier</button>';
                html += '<button class="action-btn delete" data-id="' + id + '">🗑️ Supprimer</button>';
                html += '</td>';
                html += '</tr>';
            });
        }
        
        $('#news-table-body').html(html);
    }
    
    // Sauvegarder une actualité
    function saveNews(formData) {
        $.ajax({
            url: gaisioAdmin.ajax_url,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('button[type="submit"]').text(gaisioAdmin.strings.saving).prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.data, 'success');
                    $('#gaisio-news-form')[0].reset();
                    $('#news-date').val(new Date().toISOString().split('T')[0]);
                    $('#news-order').val('0');
                    loadNews(); // Recharger le tableau
                } else {
                    showMessage(response.data, 'error');
                }
            },
            error: function() {
                showMessage(gaisioAdmin.strings.error, 'error');
            },
            complete: function() {
                $('button[type="submit"]').text('💾 Enregistrer l\'Actualité').prop('disabled', false);
            }
        });
    }
    
    // Supprimer une actualité
    $(document).on('click', '.action-btn.delete', function() {
        if (!confirm(gaisioAdmin.strings.confirm_delete)) {
            return;
        }
        
        var newsId = $(this).data('id');
        var button = $(this);
        
        $.ajax({
            url: gaisioAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'gaisio_delete_news',
                nonce: gaisioAdmin.nonce,
                news_id: newsId
            },
            beforeSend: function() {
                button.text(gaisioAdmin.strings.deleting).prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.data, 'success');
                    loadNews(); // Recharger le tableau
                } else {
                    showMessage(response.data, 'error');
                }
            },
            error: function() {
                showMessage(gaisioAdmin.strings.error, 'error');
            },
            complete: function() {
                button.text('🗑️ Supprimer').prop('disabled', false);
            }
        });
    });
    
    // Modifier une actualité (charger dans le formulaire)
    $(document).on('click', '.action-btn.edit', function() {
        var newsId = $(this).data('id');
        // Ici vous pouvez implémenter la logique pour charger les données dans le formulaire
        showMessage('Fonctionnalité de modification en cours de développement.', 'info');
    });
    
    // ========================================
    // Gestion des Utilisateurs
    // ========================================
    
    // Charger la liste des utilisateurs
    function loadUsers() {
        // Simuler le chargement des utilisateurs
        // En production, vous devriez créer un endpoint AJAX pour récupérer les utilisateurs
        var users = [
            {
                id: 1,
                username: 'admin',
                email: 'admin@gaisio.ma',
                role: 'administrator',
                registered: '2024-01-01',
                last_login: '2024-12-19'
            },
            {
                id: 2,
                username: 'utilisateur1',
                email: 'user1@gaisio.ma',
                role: 'subscriber',
                registered: '2024-06-15',
                last_login: '2024-12-18'
            }
        ];
        
        displayUsers(users);
    }
    
    // Afficher les utilisateurs dans le tableau
    function displayUsers(users) {
        var html = '';
        
        if (users.length === 0) {
            html = '<tr><td colspan="7">Aucun utilisateur trouvé.</td></tr>';
        } else {
            users.forEach(function(user) {
                html += '<tr>';
                html += '<td><div class="user-avatar">' + user.username.charAt(0).toUpperCase() + '</div></td>';
                html += '<td><strong>' + user.username + '</strong></td>';
                html += '<td>' + user.email + '</td>';
                html += '<td>' + getRoleLabel(user.role) + '</td>';
                html += '<td>' + formatDate(user.registered) + '</td>';
                html += '<td>' + formatDate(user.last_login) + '</td>';
                html += '<td class="action-buttons">';
                html += '<button class="action-btn role" data-id="' + user.id + '">👤 Rôle</button>';
                html += '<button class="action-btn delete" data-id="' + user.id + '">🗑️ Supprimer</button>';
                html += '</td>';
                html += '</tr>';
            });
        }
        
        $('#users-table-body').html(html);
    }
    
    // ========================================
    // Statistiques
    // ========================================
    
    // Charger les statistiques
    function loadStats() {
        // Simuler le chargement des statistiques
        // En production, vous devriez créer un endpoint AJAX pour récupérer les vraies données
        
        $('#total-earthquakes').text('156');
        $('#total-users').text('89');
        $('#total-news').text('12');
        $('#monthly-earthquakes').text('23');
        
        // Charger l'activité récente
        loadRecentActivity();
    }
    
    // Charger l'activité récente
    function loadRecentActivity() {
        var activities = [
            {
                icon: '🌍',
                title: 'Nouveau tremblement de terre enregistré',
                time: 'Il y a 2 heures'
            },
            {
                icon: '👥',
                title: 'Nouvel utilisateur inscrit',
                time: 'Il y a 4 heures'
            },
            {
                icon: '📰',
                title: 'Actualité publiée',
                time: 'Il y a 1 jour'
            }
        ];
        
        var html = '';
        activities.forEach(function(activity) {
            html += '<div class="activity-item">';
            html += '<div class="activity-icon">' + activity.icon + '</div>';
            html += '<div class="activity-content">';
            html += '<div class="activity-title">' + activity.title + '</div>';
            html += '<div class="activity-time">' + activity.time + '</div>';
            html += '</div>';
            html += '</div>';
        });
        
        $('#recent-activity').html(html);
    }
    
    // ========================================
    // Utilitaires
    // ========================================
    
    // Formater une date
    function formatDate(dateString) {
        if (!dateString) return '-';
        
        var date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        
        return date.toLocaleDateString('fr-FR', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }
    
    // Obtenir le label du statut
    function getStatusLabel(status) {
        var labels = {
            'published': 'Publié',
            'draft': 'Brouillon'
        };
        return labels[status] || status;
    }
    
    // Obtenir le label du rôle
    function getRoleLabel(role) {
        var labels = {
            'administrator': 'Administrateur',
            'editor': 'Éditeur',
            'author': 'Auteur',
            'contributor': 'Contributeur',
            'subscriber': 'Abonné'
        };
        return labels[role] || role;
    }
    
    // Afficher un message
    function showMessage(message, type) {
        var messageClass = type === 'success' ? 'success-message' : 
                          type === 'error' ? 'error-message' : 'info-message';
        
        var messageHtml = '<div class="' + messageClass + '">' + message + '</div>';
        
        // Supprimer les anciens messages
        $('.success-message, .error-message, .info-message').remove();
        
        // Ajouter le nouveau message
        $('.gaisio-admin-wrap h1').after(messageHtml);
        
        // Auto-suppression après 5 secondes
        setTimeout(function() {
            $('.' + messageClass).fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    // ========================================
    // Initialisation
    // ========================================
    
    // Charger les données de l'onglet actif au chargement de la page
    if ($('#actualites').hasClass('active')) {
        loadNews();
    } else if ($('#utilisateurs').hasClass('active')) {
        loadUsers();
    } else if ($('#statistiques').hasClass('active')) {
        loadStats();
    }
    
    // Ajouter des styles CSS pour les badges de statut
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .status-badge {
                padding: 4px 8px;
                border-radius: 12px;
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .status-published {
                background: #d1e7dd;
                color: #0f5132;
            }
            .status-draft {
                background: #fff3cd;
                color: #856404;
            }
            .user-avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: #2271b1;
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                font-size: 16px;
            }
            .info-message {
                background: #cce5ff;
                border: 1px solid #b3d9ff;
                color: #004085;
                padding: 12px 16px;
                border-radius: 4px;
                margin: 15px 0;
                font-size: 14px;
            }
        `)
        .appendTo('head');
    
    // ===== GESTION DES EMAILS =====
    
    // Charger la liste des utilisateurs pour le sélecteur d'emails
    function loadUsersForEmails() {
        $.ajax({
            url: gaisioAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'gaisio_admin_get_users',
                nonce: gaisioAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    const userSelect = $('#email-user-select');
                    userSelect.empty();
                    userSelect.append('<option value="">Sélectionnez un utilisateur</option>');
                    
                    response.data.forEach(function(user) {
                        userSelect.append(`<option value="${user.ID}">${user.display_name} (${user.user_email})</option>`);
                    });
                }
            },
            error: function() {
                console.error('Erreur lors du chargement des utilisateurs');
            }
        });
    }
    
    // Gérer l'envoi d'emails
    $('#gaisio-email-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            action: 'gaisio_send_user_email',
            nonce: gaisioAdmin.nonce,
            user_id: $('#email-user-select').val(),
            subject: $('#email-subject').val(),
            message: $('#email-message').val()
        };
        
        if (!formData.user_id || !formData.subject || !formData.message) {
            alert('Veuillez remplir tous les champs obligatoires');
            return;
        }
        
        // Afficher un indicateur de chargement
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.text('📤 Envoi en cours...').prop('disabled', true);
        
        $.ajax({
            url: gaisioAdmin.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('✅ ' + response.data.message);
                    // Réinitialiser le formulaire
                    $('#gaisio-email-form')[0].reset();
                    // Recharger la liste des utilisateurs
                    loadUsersForEmails();
                } else {
                    alert('❌ ' + response.data.message);
                }
            },
            error: function() {
                alert('❌ Erreur lors de l\'envoi de l\'email');
            },
            complete: function() {
                submitBtn.text(originalText).prop('disabled', false);
            }
        });
    });
    
    // Réinitialiser le formulaire d'email
    $('#reset-email-form').on('click', function() {
        $('#gaisio-email-form')[0].reset();
    });
    
    // Charger les utilisateurs quand l'onglet emails est affiché
    $('a[href="#emails"]').on('click', function() {
        setTimeout(function() {
            loadUsersForEmails();
        }, 100);
    });

}); 