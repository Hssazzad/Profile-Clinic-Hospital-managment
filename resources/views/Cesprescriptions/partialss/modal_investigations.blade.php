<div class="modal fade" id="investigationModal" tabindex="-1" role="dialog" aria-labelledby="investigationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="investigationModalLabel">Add / Edit Investigations</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <textarea id="investigations_modal" rows="6" class="form-control"
                  placeholder="CBC, FBS, X-Ray Chest…"></textarea>
        <small class="text-muted d-block mt-1">Click “Apply” to copy into the main Investigations field.</small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="applyInvestigations()" data-dismiss="modal">Apply</button>
      </div>
    </div>
  </div>
</div>
