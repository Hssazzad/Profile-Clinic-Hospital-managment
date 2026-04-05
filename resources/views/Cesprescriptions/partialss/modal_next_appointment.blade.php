<div class="modal fade" id="nextAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="nextAppointmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="nextAppointmentModalLabel">Next Appointment</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <label class="form-label">Select Next Appointment Date</label>
        <input type="date" id="next_appointment_picker" class="form-control"
               value="{{ old('next_appointment', now()->addDays(7)->format('Y-m-d')) }}">
        <small class="text-muted">This date will be saved with the prescription.</small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal" onclick="setNextAppointment()">Save Date</button>
      </div>
    </div>
  </div>
</div>
