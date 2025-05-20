<div class="modal  fade"  data-bs-focus="false" id="show_state" tabindex="-1" role="dialog" aria-labelledby="show_stateLabel" aria-hidden="true">
    <div class="modal-dialog" role="State" style="width:600px;">
        <div class="modal-content" id="addStateForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title" id="editBookLabel">State Details</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-4">
                <div class="row">
                	<table id="detailsTable" class="table table-striped table-bordered" style="width:100%">
						<thead>
							<tr>
								<th>Name</th>
								<th>Country</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Mogadishu Office</td>
								<td>Mogadishu</td>
								<td>Active</td>
							</tr>
						</tbody>
					</table>

					<p style="margin:0; padding: 0; padding-left: 2px;" class="bold">Tax Grid</p>
					<table id="tax-grid" class="table table-striped table-bordered" style="width:100%">
						<thead>
							<tr>
								<th>Min amount</th>
								<th>Max amount</th>
								<th>Rate</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>0</td>
								<td>100</td>
								<td>1%</td>
							</tr>
						</tbody>
					</table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="width: 100px;">Close</button>
                
            </div>
        </div>
    </div>
</div>