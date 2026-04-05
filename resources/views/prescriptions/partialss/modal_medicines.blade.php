<div class="modal fade" id="medicineModal" tabindex="-1" role="dialog" aria-labelledby="medicineModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="medicineModalLabel">Add Medicines</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-sm align-middle" id="modalMedTable">
            <thead class="thead-light">
              <tr class="text-muted">
                <th>Medicine Name *</th>
                <th>Strength</th>
                <th>Dose</th>
                <th>Route</th>
                <th>Frequency</th>
                <th>Duration</th>
                <th>Timing</th>
                <th style="width:60px;">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><input class="form-control modal-medicine-name" required placeholder="Azithromycin"></td>
                <td><input class="form-control modal-strength"  placeholder="500mg"></td>
                <td><input class="form-control modal-dose"      placeholder="1+0+0"></td>
                <td><input class="form-control modal-route"     placeholder="Oral"></td>
                <td><input class="form-control modal-frequency" placeholder="OD / BD / TDS"></td>
                <td><input class="form-control modal-duration"  placeholder="3 days"></td>
                <td><input class="form-control modal-timing"    placeholder="After meal"></td>
                <td class="text-center">
                  <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeModalRow(this)">X</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addModalRow()">+ Add Another In Modal</button>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="commitModalMedicines()" data-dismiss="modal">Add To List</button>
      </div>
    </div>
  </div>
</div>
