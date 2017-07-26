(function() {
  tinymce.create('tinymce.plugins.martinusps', {
    init: function(ed, url) {
      ed.addButton('martinusps', {
        title: 'Martinus partnerský systém',
        image: url + '/martinus-16x16.png',
        onclick: function() {
          ed.windowManager.open({
            title: 'Nastavenie banneru',
            body: [{type: 'textbox', name: 'url', label: 'Url knihy'},
              {type: 'listbox',
                name: 'type',
                label: 'Typ banneru',
                'values': [
                  {text: 'Banner 468x60', value: 'banner468'},
                  {text: 'Banner 300x300', value: 'banner300'},
                  {text: 'Banner 160x600', value: 'banner160'}
                ]
              }, {type: 'listbox',
                name: 'align',
                label: 'Zarovnanie',
                'values': [
                  {text: 'Žiadne', value: ''},
                  {text: 'Vľavo', value: 'm-left'},
                  {text: 'Stred', value: 'm-center'},
                  {text: 'Vpravo', value: 'm-right'}
                ]
              }
            ],
            onsubmit: function(e) {

              // Insert content when the window form is submitted
              ed.insertContent('[martinus type="' + e.data.type + '" url="' + e.data.url + '" align="' + e.data.align + '" ]');
            }
          });
        }
      });
    },
    createControl: function(n, cm) {
      return null;
    },
    getInfo: function() {
      return {
        longname: "Martinus partnerský systém",
        author: 'Maxo Matos',
        authorurl: 'http://www.matos.sk',
        infourl: 'http://wp.matos.sk',
        version: "1.0"
      };
    }

  });

  tinymce.PluginManager.add('martinusps', tinymce.plugins.martinusps);
})();