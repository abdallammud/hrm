<!-- Bootstrap modal that has table in with columns staff No. full name, contract ended, actions -->
<div class="modal fade "  id="contractExpiredModal" tabindex="-1" role="dialog" aria-labelledby="contractExpiredModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document"  style="min-width:600px; width: 60vw; max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserLabel">Contract Expired</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped" id="contractExpiredTable">
                    <thead>
                        <tr>
                            <th>Staff No.</th>
                            <th>Full Name</th>
                            <th>Contract End</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-body contract-expired-body">
                        <!-- Data will be inserted here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor" data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Close</button>
            </div>
        </div>
    </div>
</div>