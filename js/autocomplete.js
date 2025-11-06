(function($){
    // Autocomplete
    let empty = Handlebars.compile(`
            <div class="callout">
                <p class="EmptyMessage">Your search turned up 0 results.</p>
            </div>
        `);
    $('.js-typeahead').typeahead({
        hint: true,
        minLength: 1,
        highlight: true,
        classNames: {
            input: 'Typeahead-input',
            hint: 'Typeahead-hint',
            menu: 'Typeahead-menu',
            dataset: 'list-group list-group-flush autocomplete-list',
            open: 'is-open',
            empty: 'is-empty',
            cursor: 'is-active',
            suggestion: 'Typeahead-suggestion list-group-item list-group-item-action autocomplete-list-item',
            selectable: 'Typeahead-selectable'
        }
    }, {
        limit: 250,
        async: true,
      source: function(query, syncResults, asyncResults) {
          if (query === '') {
              syncResults([]);
          } else {
              return $.get(ajax_url, {
                  query: query,
                  action: 'ajax_search'
              }, function(data) {
                  return asyncResults(data);
                  console.log('test', data);
              });
          }
      },
        displayKey: 'title',
        templates: {
              suggestion: function(data) {
                  return '<a href="' + data.link + '" target="'+ data.target +'"><span class="autocomplete-list-text">' + data.title + '</span></a>'
              },
              empty: empty,
              notFound: empty,
              pending : '<h4 class="m-0 text-center">Loading...</h4>',
              footer: ``
          }
    }).on('typeahead:asyncrequest', function() {
        console.log('request');
        $('.Typeahead-spinner').show();
    }).on('typeahead:asynccancel typeahead:asyncreceive', function() {
        console.log('cancel or recived');
        $('.Typeahead-spinner').hide();
    }).on('typeahead:selected typeahead:select', function(evt, item) {
        // do what you want with the item here
        // window.location.href = item.view;
        console.log('selected');
    });
    
    })(jQuery);