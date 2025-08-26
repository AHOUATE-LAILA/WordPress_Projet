jQuery(document).ready(function($) {
    
    // Vérification des variables AJAX
    if (typeof gaisio_ajax === 'undefined') {
        console.error('❌ Variables AJAX non disponibles!');
        console.log('Tentative de récupération des variables...');
        
        // Essayer de récupérer les variables depuis WordPress
        if (typeof wp !== 'undefined' && wp.ajax) {
            gaisio_ajax = {
                ajax_url: wp.ajax.url || '/wp-admin/admin-ajax.php',
                nonce: wp.ajax.nonce || ''
            };
            console.log('✅ Variables récupérées depuis wp.ajax');
        } else {
            console.error('❌ Impossible de récupérer les variables AJAX');
            return;
        }
    } else {
        console.log('✅ Variables AJAX disponibles:', gaisio_ajax);
    }
    
    // Gestion du formulaire de connexion
    $('#gaisio-login-form').on('submit', function(e) {
        e.preventDefault();
        
        var messageDiv = $('#gaisio-login-message');
        
        // Récupérer les données du formulaire
        var formData = {
            username: $('#login_username').val(),
            password: $('#login_password').val(),
            remember: $('#gaisio-login-form input[name="remember"]').is(':checked') ? 1 : 0
        };
        
        $.ajax({
            url: gaisio_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'gaisio_login_user',
                nonce: gaisio_ajax.nonce,
                ...formData
            },
            beforeSend: function() {
                messageDiv.html('<p>Connexion en cours...</p>');
            },
            success: function(response) {
                if (response.success) {
                    messageDiv.html('<p class="success">' + response.data + '</p>');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    messageDiv.html('<p class="error">' + response.data + '</p>');
                }
            },
            error: function() {
                messageDiv.html('<p class="error">Erreur lors de la connexion</p>');
            }
        });
    });
    
    // Gestion du formulaire d'inscription utilisateur
    $('#gaisio-register-form').on('submit', function(e) {
        e.preventDefault();
        
        var messageDiv = $('#gaisio-register-message');
        
        // Récupérer les données du formulaire
        var formData = {
            username: $('#register_username').val(),
            email: $('#register_email').val(),
            password: $('#register_password').val(),
            confirm_password: $('#register_confirm_password').val()
        };
        
        $.ajax({
            url: gaisio_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'gaisio_register_user',
                nonce: gaisio_ajax.nonce,
                ...formData
            },
            beforeSend: function() {
                messageDiv.html('<p>Création du compte en cours...</p>');
            },
            success: function(response) {
                if (response.success) {
                    messageDiv.html('<p class="success">' + response.data + '</p>');
                    $('#gaisio-register-form')[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    messageDiv.html('<p class="error">' + response.data + '</p>');
                }
            },
            error: function() {
                messageDiv.html('<p class="error">Erreur lors de la création du compte</p>');
            }
        });
    });
    
    // Gestion du formulaire de saisie des tremblements de terre
    $('#gaisio-earthquake-form').on('submit', function(e) {
        e.preventDefault();
        
        var messageDiv = $('#gaisio-earthquake-message');
        
        // Récupérer les données du formulaire
        var formData = {
            datetime_utc: $('#datetime_utc').val(),
            latitude: $('#latitude').val(),
            longitude: $('#longitude').val(),
            depth: $('#depth').val(),
            magnitude: $('#magnitude').val()
        };
        
        $.ajax({
            url: gaisio_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'gaisio_save_earthquake',
                nonce: gaisio_ajax.nonce,
                ...formData
            },
            beforeSend: function() {
                messageDiv.html('<p>Enregistrement en cours...</p>');
            },
            success: function(response) {
                if (response.success) {
                    // Extraire la magnitude du message de réponse
                    var magnitude = '';
                    if (response.data.includes('Magnitude:')) {
                        magnitude = response.data.match(/Magnitude: ([\d.]+)/)[1];
                        messageDiv.html('<div class="success-message">' +
                            '<p class="success">✅ Tremblement de terre enregistré avec succès</p>' +
                            '<div class="magnitude-result">' +
                            '<strong>🔬 Magnitude saisie :</strong> ' +
                            '<span class="magnitude-display ' + getMagnitudeClass(magnitude) + '">' + magnitude + '</span>' +
                            '</div></div>');
                    } else {
                        messageDiv.html('<p class="success">' + response.data + '</p>');
                    }
                    $('#gaisio-earthquake-form')[0].reset();
                    // Recharger le tableau des tremblements de terre
                    loadEarthquakes();
                } else {
                    messageDiv.html('<p class="error">' + response.data + '</p>');
                }
            },
            error: function() {
                messageDiv.html('<p class="error">Erreur lors de l\'enregistrement</p>');
            }
        });
    });
    
    // Fonction pour charger les tremblements de terre
    function loadEarthquakes() {
        var tbody = $('#gaisio-earthquake-tbody');
        
        $.ajax({
            url: gaisio_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'gaisio_get_earthquakes',
                nonce: gaisio_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Stocker les données originales pour le filtrage
                    window.earthquakeData = response.data;
                    
                    var html = '';
                    if (response.data.length > 0) {
                        response.data.forEach(function(earthquake) {
                            html += '<tr>';
                            html += '<td>' + formatDateTime(earthquake.datetime_utc) + '</td>';
                            html += '<td>' + parseFloat(earthquake.latitude).toFixed(6) + '</td>';
                            html += '<td>' + parseFloat(earthquake.longitude).toFixed(6) + '</td>';
                            html += '<td>' + parseFloat(earthquake.depth).toFixed(2) + '</td>';
                            html += '<td>' + formatMagnitude(earthquake.magnitude) + '</td>';
                            html += '<td class="location-commune" data-lat="' + earthquake.latitude + '" data-lng="' + earthquake.longitude + '">Chargement...</td>';
                            html += '<td class="location-province" data-lat="' + earthquake.latitude + '" data-lng="' + earthquake.longitude + '">Chargement...</td>';
                            html += '</tr>';
                        });
                    } else {
                        html = '<tr><td colspan="7">Aucun tremblement de terre enregistré</td></tr>';
                    }
                    tbody.html(html);
                    
                    // Charger les informations de localisation pour chaque ligne
                    loadLocationInfo();
                    
                    // Initialiser les filtres
                    initTableFilters();
                }
            },
            error: function() {
                tbody.html('<tr><td colspan="7">Erreur lors du chargement des données</td></tr>');
            }
        });
    }
    
    // Fonction pour charger les informations de localisation
    function loadLocationInfo() {
        $('.location-commune, .location-province').each(function() {
            var $this = $(this);
            var lat = $this.data('lat');
            var lng = $this.data('lng');
            var isCommune = $this.hasClass('location-commune');
            
            $.ajax({
                url: gaisio_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gaisio_get_location_info',
                    nonce: gaisio_ajax.nonce,
                    latitude: lat,
                    longitude: lng
                },
                success: function(response) {
                    if (response.success) {
                        if (isCommune) {
                            $this.text(response.data.commune);
                        } else {
                            $this.text(response.data.province);
                        }
                    } else {
                        $this.text('Non déterminée');
                    }
                },
                error: function() {
                    $this.text('Erreur');
                }
            });
        });
    }
    
    // Fonction pour charger les informations de localisation sur la carte
    function loadMapLocationInfo(popupContent, lat, lng) {
        $.ajax({
            url: gaisio_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'gaisio_get_location_info',
                nonce: gaisio_ajax.nonce,
                latitude: lat,
                longitude: lng
            },
            success: function(response) {
                if (response.success) {
                    var locationText = response.data.commune + ', ' + response.data.province;
                    $('.map-location[data-lat="' + lat + '"][data-lng="' + lng + '"]').text(locationText);
                } else {
                    $('.map-location[data-lat="' + lat + '"][data-lng="' + lng + '"]').text('Non déterminée');
                }
            },
            error: function() {
                $('.map-location[data-lat="' + lat + '"][data-lng="' + lng + '"]').text('Erreur');
            }
        });
    }
    
    // Fonction pour formater la date et l'heure
    function formatDateTime(dateTimeString) {
        var date = new Date(dateTimeString);
        return date.toLocaleString('fr-FR', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }
    
    // Fonction pour formater la magnitude
    function formatMagnitude(magnitude) {
        if (magnitude === null || magnitude === undefined) {
            return '-';
        }
        var colorClass = '';
        if (magnitude >= 6) {
            colorClass = 'magnitude-high';
        } else if (magnitude >= 4) {
            colorClass = 'magnitude-medium';
        } else if (magnitude >= 2) {
            colorClass = 'magnitude-low';
        }
        return '<span class="magnitude-display ' + colorClass + '">' + parseFloat(magnitude).toFixed(1) + '</span>';
    }

    // Fonction pour déterminer la classe de couleur de la magnitude
    function getMagnitudeClass(magnitude) {
        if (magnitude === null || magnitude === undefined) {
            return '';
        }
        magnitude = parseFloat(magnitude);
        if (magnitude >= 6) {
            return 'magnitude-high';
        } else if (magnitude >= 4) {
            return 'magnitude-medium';
        } else if (magnitude >= 2) {
            return 'magnitude-low';
        }
        return '';
    }
    
    // Charger les tremblements de terre au chargement de la page
    if ($('#gaisio-earthquake-tbody').length) {
        loadEarthquakes();
    }
    
    // Validation des coordonnées
    $('#latitude').on('input', function() {
        var value = parseFloat($(this).val());
        if (value < -90 || value > 90) {
            $(this).addClass('error');
        } else {
            $(this).removeClass('error');
        }
    });
    
    $('#longitude').on('input', function() {
        var value = parseFloat($(this).val());
        if (value < -180 || value > 180) {
            $(this).addClass('error');
        } else {
            $(this).removeClass('error');
        }
    });
    
    // Validation de la profondeur
    $('#depth').on('input', function() {
        var value = parseFloat($(this).val());
        if (value < 0) {
            $(this).addClass('error');
        } else {
            $(this).removeClass('error');
        }
    });
    
    // Validation de la magnitude
    $('#magnitude').on('input', function() {
        var value = parseFloat($(this).val());
        if (value < 0 || value > 10) {
            $(this).addClass('error');
        } else {
            $(this).removeClass('error');
        }
    });
    
    // Validation de la confirmation du mot de passe
    $('#confirm_password').on('input', function() {
        var password = $('#password').val();
        var confirmPassword = $(this).val();
        
        if (password !== confirmPassword) {
            $(this).addClass('error');
        } else {
            $(this).removeClass('error');
        }
    });
    
    // Définir la date et l'heure actuelles par défaut
    var now = new Date();
    var year = now.getFullYear();
    var month = String(now.getMonth() + 1).padStart(2, '0');
    var day = String(now.getDate()).padStart(2, '0');
    var hours = String(now.getHours()).padStart(2, '0');
    var minutes = String(now.getMinutes()).padStart(2, '0');
    
    var dateTimeLocal = year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;
    $('#datetime_utc').val(dateTimeLocal);
    
    // Initialiser la carte si elle existe
    if ($('#gaisio-map').length) {
        initMap();
    }
    
    // Charger les statistiques
    loadStats();
    
    // Gestion des boutons de la carte
    $('#refresh-map').on('click', function() {
        loadMapData();
    });
    
    $('#center-map').on('click', function() {
        centerMap();
    });
    
    // Fonction pour initialiser la carte
    function initMap() {
        // Utiliser Leaflet pour la carte (plus léger que Google Maps)
        if (typeof L !== 'undefined') {
            // Initialiser la carte
            window.gaisioMap = L.map('gaisio-map');
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(window.gaisioMap);
            
            // Définir les limites pour couvrir Maroc, Espagne et Algérie
            var bounds = L.latLngBounds(
                [28.0, -10.0], // Sud-Ouest (Maroc méridional)
                [44.0, 6.0]    // Nord-Est (Espagne septentrionale, Algérie orientale)
            );
            window.gaisioMap.fitBounds(bounds, { padding: [20, 20] });
            
            loadMapData();
        } else {
            // Fallback si Leaflet n'est pas chargé
            $('#gaisio-map').html('<div style="text-align: center; padding: 2rem; background: #f8f9fa; border-radius: 8px;"><h3>🗺️ Carte interactive</h3><p>Chargement de la carte...</p></div>');
        }
    }
    
    // Fonction pour charger les données de la carte
    function loadMapData() {
        $.ajax({
            url: gaisio_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'gaisio_get_earthquakes',
                nonce: gaisio_ajax.nonce
            },
            success: function(response) {
                if (response.success && typeof L !== 'undefined') {
                    // Nettoyer la carte
                    window.gaisioMap.eachLayer(function(layer) {
                        if (layer instanceof L.Marker) {
                            window.gaisioMap.removeLayer(layer);
                        }
                    });
                    
                    // Ajouter les marqueurs
                    var bounds = L.latLngBounds();
                    response.data.forEach(function(earthquake) {
                        var lat = parseFloat(earthquake.latitude);
                        var lng = parseFloat(earthquake.longitude);
                        var magnitude = earthquake.magnitude ? parseFloat(earthquake.magnitude) : 0;
                        
                                                 // Calculer l'âge du tremblement de terre
                         var earthquakeDate = new Date(earthquake.datetime_utc);
                         var now = new Date();
                         var ageInHours = (now - earthquakeDate) / (1000 * 60 * 60);
                         var ageInDays = ageInHours / 24;
                         
                         // Couleur basée sur l'âge
                         var color, borderColor;
                         if (ageInHours < 24) {
                             // Moins de 24h - rouge vif
                             color = '#ff0000';
                             borderColor = '#ff0000';
                         } else if (ageInHours < 48) {
                             // Entre 24h et 48h - rouge
                             color = '#e74c3c';
                             borderColor = '#e74c3c';
                         } else if (ageInDays < 8) {
                             // Entre 2 et 7 jours - orange
                             color = '#ff9900';
                             borderColor = '#ff9900';
                         } else if (ageInDays < 30) {
                             // Entre 8 et 30 jours - orange clair
                             color = '#ffd700';
                             borderColor = '#ffd700';
                         } else {
                             // Plus ancien - gris
                             color = '#95a5a6';
                             borderColor = '#7f8c8d';
                             
                         }
                                                 // Bordure noire si magnitude élevée
                         if (magnitude >= 4) {
                             borderColor = '#000';
                         }
                        
                        if (ageInHours < 24) {
                            // Marqueur animé pour le plus récent (24h)
                            var marker = L.marker([lat, lng], {
                                icon: L.divIcon({
                                    className: 'gaisio-marker-latest',
                                    iconSize: [32, 32],
                                    iconAnchor: [16, 16],
                                    html: '<span class="pulse-marker"></span>'
                                })
                            }).addTo(window.gaisioMap);
                        } else {
                            // Marqueur classique
                        var marker = L.circleMarker([lat, lng], {
                            radius: Math.max(5, magnitude * 3),
                            fillColor: color,
                                color: borderColor,
                                weight: magnitude >= 4 ? 3 : 2,
                            opacity: 1,
                            fillOpacity: 0.8
                        }).addTo(window.gaisioMap);
                        }
                        
                        // Popup avec les informations
                        var popupContent = '<div style="min-width: 250px;">' +
                            '<h4 style="margin: 0 0 0.5rem 0; color: #2c3e50;">🌍 Tremblement de terre</h4>' +
                            '<p><strong>Date:</strong> ' + formatDateTime(earthquake.datetime_utc) + '</p>' +
                            '<p><strong>Magnitude:</strong> ' + (magnitude || 'Non spécifiée') + '</p>' +
                            '<p><strong>Profondeur:</strong> ' + parseFloat(earthquake.depth).toFixed(2) + ' km</p>' +
                            '<p><strong>Coordonnées:</strong> ' + lat.toFixed(4) + ', ' + lng.toFixed(4) + '</p>' +
                            '<p><strong>Localisation:</strong> <span class="map-location" data-lat="' + lat + '" data-lng="' + lng + '">Chargement...</span></p>' +
                            '</div>';
                        
                        // Charger les informations de localisation pour le popup
                        loadMapLocationInfo(popupContent, lat, lng);
                        
                        marker.bindPopup(popupContent);
                        bounds.extend([lat, lng]);
                    });
                    
                    // Ajuster la vue - si des marqueurs existent, les inclure, sinon garder la vue sur Maroc-Espagne
                    if (bounds.isValid() && response.data.length > 0) {
                        // Étendre les limites pour inclure Maroc-Espagne même si les marqueurs sont ailleurs
                        var regionBounds = L.latLngBounds(
                            [27.6621, -13.1684], // Sud-ouest du Maroc
                            [43.7904, 3.3372]    // Nord-est de l'Espagne
                        );
                        var combinedBounds = bounds.extend(regionBounds);
                        window.gaisioMap.fitBounds(combinedBounds, { padding: [20, 20] });
                    } else {
                        // Si aucun marqueur, centrer sur la région Maroc-Espagne
                        window.gaisioMap.setView([35.0, -5.0], 5);
                    }
                }
            }
        });
    }
    
    // Fonction pour centrer la carte sur la région Maroc-Espagne
    function centerMap() {
        if (window.gaisioMap) {
            // Définir les limites pour couvrir Maroc, Espagne et Algérie
            var bounds = L.latLngBounds(
                [28.0, -10.0], // Sud-Ouest (Maroc méridional)
                [44.0, 6.0]    // Nord-Est (Espagne septentrionale, Algérie orientale)
            );
            window.gaisioMap.fitBounds(bounds, { padding: [20, 20] });
        }
    }
    
    // Fonction pour charger les statistiques
    function loadStats() {
        $.ajax({
            url: gaisio_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'gaisio_get_stats',
                nonce: gaisio_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#total-earthquakes').text(response.data.total_earthquakes || 0);
                    $('#total-users').text(response.data.total_users || 0);
                    $('#latest-magnitude').text(response.data.latest_magnitude || '0.0');
                }
            }
        });
    }
    
    // Fonction pour initialiser les filtres du tableau
    function initTableFilters() {
        // Variables pour le tri
        var currentSort = { column: null, direction: 'asc' };
        
        // Fonction de filtrage global
        function filterTable() {
            var searchTerm = $('#global-search').val().toLowerCase();
            var visibleCount = 0;
            
            $('#gaisio-earthquake-tbody tr').each(function() {
                var $row = $(this);
                var show = true;
                
                // Vérifier si c'est une ligne de données (pas d'en-tête ou d'erreur)
                if ($row.find('td').length === 7) {
                    var datetime = $row.find('td:eq(0)').text().toLowerCase();
                    var latitude = $row.find('td:eq(1)').text().toLowerCase();
                    var longitude = $row.find('td:eq(2)').text().toLowerCase();
                    var depth = $row.find('td:eq(3)').text().toLowerCase();
                    var magnitude = $row.find('td:eq(4)').text().toLowerCase();
                    var commune = $row.find('td:eq(5)').text().toLowerCase();
                    var province = $row.find('td:eq(6)').text().toLowerCase();
                    
                    // Recherche dans tous les champs
                    if (searchTerm && 
                        !datetime.includes(searchTerm) && 
                        !latitude.includes(searchTerm) && 
                        !longitude.includes(searchTerm) && 
                        !depth.includes(searchTerm) && 
                        !magnitude.includes(searchTerm) && 
                        !commune.includes(searchTerm) && 
                        !province.includes(searchTerm)) {
                        show = false;
                    }
                }
                
                if (show) {
                    $row.show();
                    visibleCount++;
                } else {
                    $row.hide();
                }
            });
            
            // Afficher/masquer le message "aucun résultat"
            if (visibleCount === 0) {
                $('#no-results').show();
            } else {
                $('#no-results').hide();
            }
        }
        
        // Fonction de tri
        function sortTable(column, direction) {
            var $tbody = $('#gaisio-earthquake-tbody');
            var $rows = $tbody.find('tr').toArray();
            
            $rows.sort(function(a, b) {
                var aVal = $(a).find('td:eq(' + column + ')').text().trim();
                var bVal = $(b).find('td:eq(' + column + ')').text().trim();
                
                // Conversion pour le tri numérique
                if (column === 1 || column === 2 || column === 3) { // lat, lng, depth
                    aVal = parseFloat(aVal) || 0;
                    bVal = parseFloat(bVal) || 0;
                } else if (column === 4) { // magnitude
                    aVal = parseFloat($(a).find('td:eq(4) .magnitude-display').text()) || 0;
                    bVal = parseFloat($(b).find('td:eq(4) .magnitude-display').text()) || 0;
                }
                
                if (direction === 'asc') {
                    return aVal > bVal ? 1 : -1;
                } else {
                    return aVal < bVal ? 1 : -1;
                }
            });
            
            $tbody.empty().append($rows);
        }
        
        // Événement pour la recherche globale
        $('#global-search').on('input', filterTable);
        
        // Événement pour effacer les filtres
        $('#clear-filters').on('click', function() {
            $('#global-search').val('');
            filterTable();
        });
        
        // Événements pour le tri
        $('.gaisio-table th[data-sort]').on('click', function() {
            var column = $(this).data('sort');
            var columnIndex = $(this).index();
            
            // Changer la direction du tri
            if (currentSort.column === column) {
                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.column = column;
                currentSort.direction = 'asc';
            }
            
            // Mettre à jour les flèches dans les en-têtes
            $('.gaisio-table th').each(function() {
                var $th = $(this);
                if ($th.data('sort') === column) {
                    $th.text($th.text().replace(' ↕', '').replace(' ↑', '').replace(' ↓', ''));
                    $th.text($th.text() + (currentSort.direction === 'asc' ? ' ↑' : ' ↓'));
                } else {
                    $th.text($th.text().replace(' ↕', '').replace(' ↑', '').replace(' ↓', '') + ' ↕');
                }
            });
            
            sortTable(columnIndex, currentSort.direction);
        });
    }
    
    // Fonction pour le carrousel des actualités avec mouvement amélioré
    function initCarousel() {
        var slides = $('.carousel-slide');
        var indicators = $('.indicator');
        var totalSlides = slides.length;
        var autoPlayInterval;
        var currentIndex = 0;
        var isAnimating = false;

        // Afficher tous les slides (ils sont déjà côte à côte en CSS)
        slides.addClass('active');
        
        // Masquer les indicateurs et boutons de navigation car tous les slides sont visibles
        $('.carousel-indicators').hide();
        $('.carousel-btn').hide();

        // Fonction pour faire défiler les slides avec mouvement fluide
        function scrollSlides() {
            if (isAnimating) return; // Éviter les animations simultanées
            
            var container = $('.carousel-slides');
            var slideWidth = slides.first().outerWidth(true);
            var containerWidth = container.width();
            var maxScroll = container[0].scrollWidth - containerWidth;
            
            // Calculer la nouvelle position avec un mouvement plus fluide
            var newScroll = currentIndex * slideWidth;
            
            // Animation plus douce avec easing (optimisée pour 3s)
            isAnimating = true;
            container.animate({
                scrollLeft: newScroll
            }, {
                duration: 1000, // Animation plus rapide pour 3s
                easing: 'easeInOutQuart',
                step: function(now) {
                    // Mettre à jour l'index pendant l'animation
                    currentIndex = Math.round(now / slideWidth);
                },
                complete: function() {
                    // Passer au slide suivant
                    currentIndex++;
                    
                    // Si on arrive à la fin, revenir au début de manière fluide
                    if (currentIndex >= totalSlides) {
                        currentIndex = 0;
                        container.animate({
                            scrollLeft: 0
                        }, {
                            duration: 600, // Animation plus rapide
                            easing: 'easeInOutQuart'
                        });
                    }
                    isAnimating = false;
                }
            });
        }

        // Fonction pour faire défiler vers la droite avec effet de rebond
        function scrollToNext() {
            if (isAnimating) return;
            
            var container = $('.carousel-slides');
            var slideWidth = slides.first().outerWidth(true);
            var containerWidth = container.width();
            var maxScroll = container[0].scrollWidth - containerWidth;
            
            currentIndex++;
            
            if (currentIndex >= totalSlides) {
                // Effet de rebond à la fin
                isAnimating = true;
                container.animate({
                    scrollLeft: maxScroll
                }, {
                    duration: 800, // Animation plus rapide pour 3s
                    easing: 'easeInOutBack',
                    complete: function() {
                        // Revenir au début avec un effet fluide
                        currentIndex = 0;
                        container.animate({
                            scrollLeft: 0
                        }, {
                            duration: 600, // Animation plus rapide
                            easing: 'easeInOutQuart',
                            complete: function() {
                                isAnimating = false;
                            }
                        });
                    }
                });
            } else {
                // Défilement normal avec effet de rebond léger (optimisé pour 3s)
                isAnimating = true;
                container.animate({
                    scrollLeft: currentIndex * slideWidth
                }, {
                    duration: 800, // Animation plus rapide
                    easing: 'easeInOutBack',
                    complete: function() {
                        isAnimating = false;
                    }
                });
            }
        }

        // Démarrer le défilement automatique avec intervalle variable
        function startAutoPlay() {
            // Récupérer l'intervalle depuis l'attribut data-interval ou utiliser 3000ms par défaut
            var interval = $('.gaisio-news-carousel').data('interval') || 3000;
            autoPlayInterval = setInterval(scrollToNext, interval);
            console.log('🎠 Carrousel automatique démarré avec intervalle:', interval + 'ms');
        }

        // Arrêter le défilement automatique
        function stopAutoPlay() {
            clearInterval(autoPlayInterval);
        }

        // Pause au survol avec effet de transition
        $('.carousel-container').on('mouseenter', function() {
            stopAutoPlay();
            $(this).addClass('paused');
        });
        
        $('.carousel-container').on('mouseleave', function() {
            $(this).removeClass('paused');
            startAutoPlay();
        });

        // Ajouter des contrôles tactiles pour mobile
        var startX, startY, distX, distY;
        var threshold = 50; // Distance minimale pour déclencher le swipe
        
        $('.carousel-slides').on('touchstart', function(e) {
            var touch = e.originalEvent.touches[0];
            startX = touch.clientX;
            startY = touch.clientY;
        });
        
        $('.carousel-slides').on('touchmove', function(e) {
            e.preventDefault();
        });
        
        $('.carousel-slides').on('touchend', function(e) {
            var touch = e.originalEvent.changedTouches[0];
            distX = touch.clientX - startX;
            distY = touch.clientY - startY;
            
            if (Math.abs(distX) > Math.abs(distY) && Math.abs(distX) > threshold) {
                if (distX > 0) {
                    // Swipe vers la droite - slide précédent
                    scrollToPrevious();
                } else {
                    // Swipe vers la gauche - slide suivant
                    scrollToNext();
                }
            }
        });

        // Fonction pour le slide précédent
        function scrollToPrevious() {
            if (isAnimating) return;
            
            currentIndex--;
            if (currentIndex < 0) {
                currentIndex = totalSlides - 1;
            }
            
            var container = $('.carousel-slides');
            isAnimating = true;
            container.animate({
                scrollLeft: currentIndex * slides.first().outerWidth(true)
            }, {
                duration: 800, // Animation plus rapide pour 3s
                easing: 'easeInOutBack',
                complete: function() {
                    isAnimating = false;
                }
            });
        }

        // Démarrer le carrousel
        startAutoPlay();
    }

    // Fonction pour charger les actualités dynamiquement
    function loadNewsCarousel() {
        console.log('🔍 Chargement des actualités...');
        console.log('URL AJAX:', gaisio_ajax.ajax_url);
        console.log('Nonce:', gaisio_ajax.nonce);
        
        // Afficher un message de chargement
        $('#gaisio-news-slides').html('<div style="text-align: center; padding: 20px; color: #666;">Chargement des actualités...</div>');
        
        $.ajax({
            url: gaisio_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'gaisio_get_news_frontend',
                nonce: gaisio_ajax.nonce
            },
            beforeSend: function() {
                console.log('📡 Envoi de la requête AJAX...');
            },
            success: function(response) {
                console.log('✅ Réponse reçue:', response);
                if (response.success && response.data.length > 0) {
                    console.log('📰 Actualités trouvées:', response.data.length);
                    displayNewsCarousel(response.data);
                    initCarousel();
                } else {
                    console.log('❌ Aucune actualité trouvée ou erreur');
                    $('#gaisio-news-slides').html('<div style="text-align: center; padding: 20px; color: #666;">Aucune actualité disponible pour le moment.</div>');
                }
            },
            error: function(xhr, status, error) {
                console.log('❌ Erreur AJAX:', status, error);
                console.log('Réponse XHR:', xhr.responseText);
                $('#gaisio-news-slides').html('<div style="text-align: center; padding: 20px; color: red;">Erreur lors du chargement des actualités.</div>');
            }
        });
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
    
    // Fonction pour afficher les actualités dans le carrousel
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
    
    // Initialiser le carrousel si il existe
    console.log('🔍 Vérification du carrousel d\'actualités...');
    console.log('Éléments trouvés:', $('.gaisio-news-carousel').length);
    console.log('Éléments trouvés (ID):', $('#gaisio-news-section').length);
    console.log('Variables AJAX disponibles:', typeof gaisio_ajax !== 'undefined');
    if (typeof gaisio_ajax !== 'undefined') {
        console.log('URL AJAX:', gaisio_ajax.ajax_url);
        console.log('Nonce:', gaisio_ajax.nonce);
    }
    
    if ($('.gaisio-news-carousel').length) {
        console.log('✅ Carrousel trouvé, chargement des actualités...');
        loadNewsCarousel();
    } else {
        console.log('❌ Aucun carrousel trouvé sur la page');
    }
    
    // Écouter les notifications de mise à jour des actualités
    window.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'gaisio_news_updated') {
            console.log('📢 Notification reçue:', event.data.message);
            if ($('.gaisio-news-carousel').length) {
                console.log('🔄 Rafraîchissement automatique du carrousel...');
                loadNewsCarousel();
            }
        }
    });
    
    // Système de vérification périodique des actualités (toutes les 30 secondes)
    if ($('.gaisio-news-carousel').length) {
        var lastNewsCount = 0;
        
        setInterval(function() {
            $.ajax({
                url: gaisio_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gaisio_get_news_frontend',
                    nonce: gaisio_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var currentCount = response.data.length;
                        if (currentCount !== lastNewsCount && lastNewsCount > 0) {
                            console.log('🆕 Nouvelles actualités détectées! Mise à jour automatique...');
                            displayNewsCarousel(response.data);
                            if (typeof initCarousel === 'function') {
                                initCarousel();
                            }
                        }
                        lastNewsCount = currentCount;
                    }
                }
            });
        }, 30000); // Vérifier toutes les 30 secondes
    }
    
}); 