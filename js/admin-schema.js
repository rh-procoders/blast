/**
 * Schema Admin JavaScript
 * 
 * Provides enhanced functionality for the schema options pages
 */

jQuery(document).ready(function($) {
    'use strict';
    
    // Initialize admin interface
    SchemaAdmin.init();
});

var SchemaAdmin = {
    
    init: function() {
        this.bindEvents();
        this.initLocationPreviews();
        this.initServiceTags();
        this.initValidationTools();
        this.addHelpTexts();
    },
    
    bindEvents: function() {
        // Export configuration
        $(document).on('click', '#export-schema-btn', this.exportConfig);
        
        // Import configuration
        $(document).on('change', 'input[name="acf[field_import_schema_config]"]', this.importConfig);
        
        // Reset configuration
        $(document).on('click', '#reset-schema-btn', this.resetConfig);
        
        // Validate schema
        $(document).on('click', '#validate-schema-btn', this.validateSchema);
        
        // Auto-generate service URLs
        $(document).on('blur', 'input[data-name="service_name"]', this.generateServiceURL);
        
        // Auto-format phone numbers
        $(document).on('blur', 'input[data-name*="phone"]', this.formatPhoneNumber);
        
        // Preview location addresses
        $(document).on('change', 'input[data-name*="address"], select[data-name*="address"]', this.updateLocationPreview);
        
        // Social media URL validation
        $(document).on('blur', 'input[data-name="social_url"]', this.validateSocialURL);
    },
    
    exportConfig: function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var originalText = $button.text();
        
        $button.text('Exporting...').prop('disabled', true);
        
        $.post(schemaAdmin.ajaxurl, {
            action: 'export_schema_config',
            nonce: schemaAdmin.nonce
        }, function(response) {
            // The export is handled via direct download
            $button.text(originalText).prop('disabled', false);
        }).fail(function() {
            alert('Export failed. Please try again.');
            $button.text(originalText).prop('disabled', false);
        });
    },
    
    importConfig: function(e) {
        var file = e.target.files[0];
        
        if (!file) return;
        
        if (!file.name.endsWith('.json')) {
            alert('Please select a valid JSON file.');
            return;
        }
        
        if (!confirm(schemaAdmin.strings.confirmImport)) {
            $(this).val('');
            return;
        }
        
        var formData = new FormData();
        formData.append('action', 'import_schema_config');
        formData.append('nonce', schemaAdmin.nonce);
        formData.append('config_file', file);
        
        $.ajax({
            url: schemaAdmin.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(schemaAdmin.strings.importSuccess);
                    location.reload();
                } else {
                    alert('Import failed: ' + response.data);
                }
            },
            error: function() {
                alert('Import failed. Please try again.');
            }
        });
    },
    
    resetConfig: function(e) {
        e.preventDefault();
        
        if (!confirm(schemaAdmin.strings.confirmReset)) {
            return;
        }
        
        var $button = $(this);
        var originalText = $button.text();
        
        $button.text('Resetting...').prop('disabled', true);
        
        $.post(schemaAdmin.ajaxurl, {
            action: 'reset_schema_config',
            nonce: schemaAdmin.nonce
        }, function(response) {
            if (response.success) {
                alert(schemaAdmin.strings.resetSuccess);
                location.reload();
            } else {
                alert('Reset failed: ' + response.data);
            }
            $button.text(originalText).prop('disabled', false);
        }).fail(function() {
            alert('Reset failed. Please try again.');
            $button.text(originalText).prop('disabled', false);
        });
    },
    
    validateSchema: function(e) {
        e.preventDefault();
        
        var url = $('#schema-validation-url').val() || window.location.origin;
        var $button = $(this);
        var originalText = $button.text();
        
        $button.text('Validating...').prop('disabled', true);
        
        $.post(schemaAdmin.ajaxurl, {
            action: 'validate_schema_markup',
            nonce: schemaAdmin.nonce,
            url: url
        }, function(response) {
            var $results = $('#schema-validation-results');
            
            if (response.success) {
                $results.removeClass('invalid').addClass('valid')
                    .html('<h4>Validation Results</h4><p>' + response.data.message + '</p>');
                
                if (response.data.schemas.length > 0) {
                    var html = '<h5>Found Schema Types:</h5><ul>';
                    response.data.schemas.forEach(function(schema) {
                        if (schema['@type']) {
                            html += '<li>' + schema['@type'] + '</li>';
                        }
                    });
                    html += '</ul>';
                    $results.append(html);
                }
            } else {
                $results.removeClass('valid').addClass('invalid')
                    .html('<h4>Validation Failed</h4><p>' + response.data + '</p>');
            }
            
            $button.text(originalText).prop('disabled', false);
        }).fail(function() {
            $('#schema-validation-results').removeClass('valid').addClass('invalid')
                .html('<h4>Validation Error</h4><p>Could not validate schema markup.</p>');
            $button.text(originalText).prop('disabled', false);
        });
    },
    
    generateServiceURL: function() {
        var serviceName = $(this).val();
        var $urlField = $(this).closest('.acf-row').find('input[data-name="service_url"]');
        
        if (serviceName && !$urlField.val()) {
            var slug = serviceName.toLowerCase()
                .replace(/ä/g, 'ae').replace(/ö/g, 'oe').replace(/ü/g, 'ue').replace(/ß/g, 'ss')
                .replace(/[^a-z0-9]/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
            
            $urlField.val(window.location.origin + '/services/' + slug);
        }
    },
    
    formatPhoneNumber: function() {
        var phone = $(this).val();
        
        if (!phone) return;
        
        // Basic German phone number formatting
        phone = phone.replace(/\D/g, '');
        
        if (phone.startsWith('49')) {
            phone = '+49 ' + phone.substring(2);
        } else if (phone.startsWith('0')) {
            phone = '+49 ' + phone.substring(1);
        }
        
        // Format with spaces
        if (phone.startsWith('+49')) {
            phone = phone.replace(/(\+49)(\d{2,4})(\d+)/, function(match, country, area, number) {
                return country + ' ' + area + ' ' + number.replace(/(\d{2})/g, '$1 ').trim();
            });
        }
        
        $(this).val(phone);
    },
    
    updateLocationPreview: function() {
        var $row = $(this).closest('.acf-row');
        var $preview = $row.find('.schema-location-preview');
        
        if ($preview.length === 0) {
            $preview = $('<div class="schema-location-preview"></div>');
            $row.find('.acf-fields').first().append($preview);
        }
        
        var street = $row.find('input[data-name="street_address"]').val();
        var city = $row.find('input[data-name="address_locality"]').val();
        var postal = $row.find('input[data-name="postal_code"]').val();
        var country = $row.find('select[data-name="address_country"]').val();
        
        if (street || city || postal) {
            var address = [street, postal + ' ' + city, country].filter(Boolean).join(', ');
            $preview.html('<strong>Address Preview:</strong> ' + address);
        } else {
            $preview.empty();
        }
    },
    
    validateSocialURL: function() {
        var url = $(this).val();
        var platform = $(this).closest('.acf-row').find('select[data-name="social_platform"]').val();
        
        if (!url || !platform) return;
        
        var platformDomains = {
            'facebook': 'facebook.com',
            'instagram': 'instagram.com',
            'twitter': ['twitter.com', 'x.com'],
            'linkedin': 'linkedin.com',
            'youtube': 'youtube.com',
            'tiktok': 'tiktok.com',
            'xing': 'xing.com',
            'pinterest': 'pinterest.com'
        };
        
        var expectedDomains = platformDomains[platform];
        if (!expectedDomains) return;
        
        if (typeof expectedDomains === 'string') {
            expectedDomains = [expectedDomains];
        }
        
        var isValid = expectedDomains.some(function(domain) {
            return url.includes(domain);
        });
        
        if (!isValid) {
            $(this).css('border-color', '#dc3232');
            $(this).after('<div class="schema-validation-error" style="color: #dc3232; font-size: 12px; margin-top: 5px;">URL does not match selected platform</div>');
        } else {
            $(this).css('border-color', '');
            $(this).siblings('.schema-validation-error').remove();
        }
    },
    
    initLocationPreviews: function() {
        // Add preview containers to existing location rows
        $('.acf-field[data-name="business_locations"] .acf-row').each(function() {
            SchemaAdmin.updateLocationPreview.call($(this).find('input').first());
        });
    },
    
    initServiceTags: function() {
        // Add visual tags for selected subjects
        $(document).on('change', 'input[data-name="service_subjects"]', function() {
            var $row = $(this).closest('.acf-row');
            var $container = $row.find('.schema-service-tags');
            
            if ($container.length === 0) {
                $container = $('<div class="schema-service-tags"></div>');
                $(this).closest('.acf-field').after($container);
            }
            
            $container.empty();
            
            $row.find('input[data-name="service_subjects"]:checked').each(function() {
                var label = $(this).next('label').text();
                $container.append('<span class="schema-service-tag">' + label + '</span>');
            });
        });
    },
    
    initValidationTools: function() {
        // Add validation tools to the tools page
        if ($('#schema-validation-tools').length > 0) {
            var toolsHTML = `
                <div class="schema-tools-grid">
                    <div class="schema-tool-card">
                        <h4>Validate Current Page</h4>
                        <p>Test the schema markup on your website</p>
                        <input type="url" id="schema-validation-url" placeholder="Enter URL to validate" style="width: 100%; margin-bottom: 10px;">
                        <button type="button" id="validate-schema-btn" class="button button-primary">Validate Schema</button>
                    </div>
                    <div class="schema-tool-card">
                        <h4>Google Rich Results Test</h4>
                        <p>Test with Google's official tool</p>
                        <a href="https://search.google.com/test/rich-results" target="_blank" class="button button-secondary">Open Google Tool</a>
                    </div>
                    <div class="schema-tool-card">
                        <h4>Schema.org Validator</h4>
                        <p>Validate against Schema.org standards</p>
                        <a href="https://validator.schema.org/" target="_blank" class="button button-secondary">Open Validator</a>
                    </div>
                </div>
                <div id="schema-validation-results" class="schema-validation-results" style="display: none;"></div>
            `;
            $('#schema-validation-tools').html(toolsHTML);
        }
    },
    
    addHelpTexts: function() {
        // Add contextual help texts
        var helpTexts = {
            'business_name': 'This will appear in search results and schema markup',
            'business_phone': 'Use international format: +49 30 12345678',
            'business_email': 'This email will be included in schema markup',
            'opening_hours': 'Use format: Mo-Fr 09:00-18:00, Sa 09:00-14:00',
            'service_name': 'Be specific and descriptive for better SEO',
            'service_subjects': 'Select all relevant subjects for this service',
            'social_url': 'Make sure the URL matches the selected platform'
        };
        
        Object.keys(helpTexts).forEach(function(fieldName) {
            var $field = $('.acf-field[data-name="' + fieldName + '"]');
            if ($field.length > 0 && $field.find('.schema-help-text').length === 0) {
                $field.find('.acf-input').append(
                    '<div class="schema-help-text">' + helpTexts[fieldName] + '</div>'
                );
            }
        });
    }
};

// Auto-save notifications
$(document).on('acf/save_post', function(e, $el) {
    if ($el.find('.acf-field[data-name*="schema"]').length > 0) {
        // Show save notification
        $('<div class="notice notice-success is-dismissible"><p>Schema configuration saved successfully!</p></div>')
            .insertAfter('.wrap h1').delay(3000).fadeOut();
    }
});