(function(jsGrid, $) {
  var NumberField = jsGrid.NumberField;

  function DecimalField(config) {
    NumberField.call(this, config);
  }

  DecimalField.prototype = new NumberField({

    step: 0.01,

    filterValue: function() {
      return this.filterControl.val() ? parseFloat(this.filterControl.val()) : undefined;
    },

    insertValue: function() {
      return this.insertControl.val() ? parseFloat(this.insertControl.val()) : undefined;
    },

    editValue: function() {
      return this.editControl.val() ? parseFloat(this.editControl.val()) : undefined;
    },

    _createTextBox: function() {
      return NumberField.prototype._createTextBox.call(this)
        .attr("step", this.step);
    }
  });

  jsGrid.fields.decimal = jsGrid.DecimalField = DecimalField;

}(jsGrid, jQuery));

$(function() {
    /*$.when(
      $.ajax({type: "GET", url: "/docenti/"}),
      $.ajax({type: "GET", url: "/progetti/"})
    ).then(
      (docenti, progetti) => {*/

      tipoOre = [
          { id: 0, descrizione: "" },
          { id: 1, descrizione: "Realizzazione - Doc. retribuita"},
          { id: 2, descrizione: "Realizzazione - Doc. in obbligo"},
          { id: 3, descrizione: "Realizzazione - A/T retribuita"},
          { id: 4, descrizione: "Realizzazione - A/T in obbligo"},
          { id: 5, descrizione: "Progettazione - Retribuita"},
          { id: 6, descrizione: "Progettazione - In obbligo"},
      ];

        $("#jsGrid").jsGrid({
            width: "100%",
            filtering: true,
            editing: true,
            sorting: true,
            paging: true,
            autoload: true,
            pageSize: 10,
            pageButtonCount: 5,
            noDataContent: "Nessuna ora dichiarata",
            deleteConfirm: "Vuoi veramente rimuovere questa riga?",

            onItemEditing: function(args) {
              // blocca l'editing delle righe con campo 'concluso' = 1
                if(args.item.concluso === 1) {
                    args.cancel = true;
                }
            },

            controller: {
                loadData: function(filter) {
                    return $.ajax({
                        type: "GET",
                        url: "ore/",
                        data: filter
                    });
                },
                insertItem: function(item) {
                    return $.ajax({
                        type: "POST",
                        url: "ore/",
                        data: item
                    });
                },
                updateItem: function(item) {
                    return $.ajax({
                        type: "POST",
                        url: "aggiornaOre.php",
                        data: item
                    });
                },
                deleteItem: function(item) {
                    return $.ajax({
                        type: "POST",
                        url: "eliminaOre.php",
                        data: item
                    });
                }
            },
            fields: [
                { name: "progetto", title: "Progetto", type: "text", width: 100, editing: false },
                { name: "data", title: "Data", type: "text", width: 100 },
                { name: "ora", title: "Ora", type: "text", width: 100 },
                { name: "nOre", title: "N. Ore", type: "decimal", width: 50 , filtering: false, step: 0.5 },
                { name: "tipologiaOre", title: "Tipo Ore", type: "select", width: 100 , items: tipoOre, valueField: "id", textField: "descrizione" },
                { type: "control",
                    searchModeButtonTooltip: "Passa alla modalità di ricerca",
                    insertModeButtonTooltip: "Passa alla modalità di inserimento",
                    editButton:true,
                    editButtonTooltip: "Modifica",
                    deleteButtonTooltip: "Elimina",
                    searchButtonTooltip: "Cerca",
                    clearFilterButtonTooltip: "Pulisci filtri",
                    insertButtonTooltip: "Inserisci",
                    updateButtonTooltip: "Aggiorna",
                    cancelEditButtonTooltip: "Annulla le modifiche",

                    align:"center",
                    itemTemplate: function(value, item) {
                      var $result = $([]);
                      if( item.concluso == 0 ) {
                        $result = $result.add(this._createEditButton(item));
                        $result = $result.add(this._createDeleteButton(item));
                      }
                      return $result;
                    },
                }
            ]
        //});
    });
    $("#grid").jsGrid("sort", 0);
});
