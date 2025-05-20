<!-- Upload transaction -->
<div class="modal fade"   data-bs-focus="false" id="edit_table_customize" tabindex="-1" role="dialog" aria-labelledby="transaction_uploadLabel" aria-hidden="true">
    <div class="modal-dialog" role="edit_table_customize" style="width:600px;">
        <form class="modal-content" id="customizeTableForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Customize table</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <label class="cursor col col-xs-12 col-md-12">
                        	<input type="hidden" id="dt_table" name="">
                            <div class="row" style="font-size: 14px;" id="allColumns">
                            	
                            
                            </div>
                            <span class="file-selected-name"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Save changes</button>
            </div>
        </form>
    </div>
</div>


<script type="text/javascript">
	addEventListener("DOMContentLoaded", (event) => {
		// Customize table
		$('.edit-table_customize').on('click', (e) => {
			let table = $(e.currentTarget).attr('data-table');
			let modal = $('#edit_table_customize');
			$(modal).find('#dt_table').val(table);
			$.post(`${base_url}/app/payroll_controller.php?action=get&endpoint=allColumns4CustomizeTable`, {table:table}, function(data) {
				// console.log(data)
				$(modal).find('#allColumns').html(data);
			});

		    $(modal).modal('show');
		})

		// Submit form
		$('#customizeTableForm').on('submit', (e) => {
			let form = $(e.target);
			let slctedColumns = [];
			let table = $(form).find('#dt_table').val();
			$(form).find('input.custom-col:checked').each((i, el) => {
				if($(el).val()) {
					slctedColumns.push($(el).val())
				}
			})

			if(slctedColumns.length < 1) {
				swal('Ooops', 'Please select at least one column', 'error');
				return false;
			}

			$.post(`${base_url}/app/payroll_controller.php?action=update&endpoint=columns4CustomizeTable`, {table:table, columns:slctedColumns}, function(data) {
				// console.log(data)
				location.reload();
			});

			return false
		})

	});
</script>