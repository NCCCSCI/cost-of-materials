$(document).ready(function () {
    // get all the elements with an id that starts with table
    let tables = document.querySelectorAll("[id^='table-']");

    for (t of tables) {
    // Setup - add a text input to each footer cell
    $(`#${t.id} thead tr`)
        .clone(true)
        .addClass('filters')
        .appendTo(`#${t.id} thead`);
 
    let table = $(t).DataTable({
        orderCellsTop: true,
        pageLength: 100,
        fixedHeader: true,
	layout: {
		topEnd: {
			buttons: [ 'excel','print' ]
		}
	},
	autoWidth: false,
        initComplete: function () {
            let api = this.api();
	    let cursorPosition;
 
            // For each column
            api
                .columns()
                .eq(0)
                .each(function (colIdx) {
                    // Set the header cell to contain the input element
                    let cell = $(`#${t.id} .filters th`).eq(
                        $(api.column(colIdx).header()).index()
                    );
                    let title = $(cell).text();
                    $(cell).html('<input type="text" placeholder="' + title + '">');
 
                    // On every keypress in this input
                    $(
                        'input',
                        $(`#${t.id} .filters th`).eq($(api.column(colIdx).header()).index())
                    )
                        .off('keyup change')
                        .on('change', function (e) {
                            // Get the search value
                            $(this).attr('title', $(this).val());
                            let regexr = '({search})'; //$(this).parents('th').find('select').val();
 
                            cursorPosition = this.selectionStart;
                            // Search the column for that value
                            api
                                .column(colIdx)
                                .search(
                                    this.value != ''
                                        ? regexr.replace('{search}', '(((' + this.value + ')))')
                                        : '',
                                    this.value != '',
                                    this.value == ''
                                )
                                .draw();
                        })
                        .on('keyup', function (e) {
                            e.stopPropagation();
 
                            $(this).trigger('change');
                            $(this)
                                .focus()[0]
                                .setSelectionRange(cursorPosition, cursorPosition);
                        });
                });
        },
    });
    }
});
