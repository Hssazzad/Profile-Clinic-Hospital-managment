<!-- Add this to your AdminLTE sidebar menu -->
<li class="nav-item">
    <a href="{{ route('prescriptions.worldclass.create') }}" class="nav-link">
        <i class="nav-icon fas fa-file-medical-alt"></i>
        <p>World-Class Rx</p>
    </a>
</li>

<!-- Or add this button to your dashboard -->
<a href="{{ route('prescriptions.worldclass.create') }}" class="btn btn-primary btn-lg">
    <i class="fas fa-file-medical-alt mr-2"></i>
    World-Class Prescription
</a>
