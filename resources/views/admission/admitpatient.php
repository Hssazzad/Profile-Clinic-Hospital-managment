<?php
// resources/views/admission/admitpatient.php

// Load AdminLTE layout manually
// If your layout uses sections, you must include header/footer yourself

// Example header include:
//include resource_path('views/adminlte/header.php');
?>

<h1>Admit Patient</h1>

<?php if (session('success')): ?>
  <div class="alert alert-success">
      <?= session('success'); ?>
  </div>
<?php endif; ?>

<?php if (session('error')): ?>
  <div class="alert alert-danger">
      <?= session('error'); ?>
  </div>
<?php endif; ?>

<?php if ($errors->any()): ?>
  <div class="alert alert-danger">
    <b>Fix the following:</b>
    <ul class="mb-0">
      <?php foreach ($errors->all() as $e): ?>
        <li><?= $e; ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>


<div class="card">
  <div class="card-body">

    <form method="POST" action="<?= route('admission.admitpatient.store'); ?>">
      <?php echo csrf_field(); ?>

      <!-- Select patient -->
      <div class="form-group">
        <label for="patient_id"><b>Select Patient</b></label>

        <select name="patient_id" id="patient_id"
                class="form-control <?= $errors->has('patient_id') ? 'is-invalid' : ''; ?>"
                required>
          <option value="">-- Select Patient --</option>

          <?php foreach ($patients as $p): ?>
            <option value="<?= $p->id; ?>"
              <?= old('patient_id') == $p->id ? 'selected' : ''; ?>>
              <?= $p->patientname; ?>
              (<?= $p->mobile_no ?? $p->mobileno; ?>)
            </option>
          <?php endforeach; ?>

        </select>

        <?php if ($errors->has('patient_id')): ?>
          <span class="invalid-feedback">
            <?= $errors->first('patient_id'); ?>
          </span>
        <?php endif; ?>
      </div>

      <hr>

      <!-- Admission details -->
      <div class="row">

        <!-- Admit date -->
        <div class="col-md-4">
          <div class="form-group">
            <label for="admit_date">Admit Date</label>
            <input type="date" id="admit_date" name="admit_date"
                   value="<?= old('admit_date', now()->toDateString()); ?>"
                   class="form-control <?= $errors->has('admit_date') ? 'is-invalid' : ''; ?>"
                   required>

            <?php if ($errors->has('admit_date')): ?>
              <span class="invalid-feedback">
                <?= $errors->first('admit_date'); ?>
              </span>
            <?php endif; ?>
          </div>
        </div>

        <!-- Ward -->
        <div class="col-md-4">
          <div class="form-group">
            <label for="ward">Ward</label>
            <input type="text" id="ward" name="ward"
                   value="<?= old('ward'); ?>"
                   class="form-control <?= $errors->has('ward') ? 'is-invalid' : ''; ?>"
                   placeholder="e.g. Medicine, Surgery">

            <?php if ($errors->has('ward')): ?>
              <span class="invalid-feedback">
                <?= $errors->first('ward'); ?>
              </span>
            <?php endif; ?>
          </div>
        </div>

        <!-- Bed -->
        <div class="col-md-4">
          <div class="form-group">
            <label for="bed_no">Bed No.</label>
            <input type="text" id="bed_no" name="bed_no"
                   value="<?= old('bed_no'); ?>"
                   class="form-control <?= $errors->has('bed_no') ? 'is-invalid' : ''; ?>"
                   placeholder="e.g. B-12">

            <?php if ($errors->has('bed_no')): ?>
              <span class="invalid-feedback">
                <?= $errors->first('bed_no'); ?>
              </span>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Reason -->
      <div class="form-group">
        <label for="reason">Reason for Admission</label>
        <textarea id="reason" name="reason" rows="3"
                  class="form-control <?= $errors->has('reason') ? 'is-invalid' : ''; ?>"
                  placeholder="Short summary of complaint / diagnosis"><?= old('reason'); ?></textarea>

        <?php if ($errors->has('reason')): ?>
          <span class="invalid-feedback">
            <?= $errors->first('reason'); ?>
          </span>
        <?php endif; ?>
      </div>

      <div class="d-flex justify-content-between">
        <a href="<?= url()->previous(); ?>" class="btn btn-secondary">Back</a>
        <button type="submit" class="btn btn-primary">
          Save &amp; Print Admission Slip
        </button>
      </div>

    </form>

  </div>
</div>

<?php
// Example footer include:
//include resource_path('views/adminlte/footer.php');
?>
