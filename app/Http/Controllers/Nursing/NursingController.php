<?php

namespace App\Http\Controllers\Nursing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NursingController extends Controller
{
    /**
     * Display the Nursing List Page
     */
    public function index()
    {
        return "Welcome to Nursing List Page";
    }

    /**
     * Create Nursing Entry
     */
    public function create()
    {
        if (view()->exists('nursing.create')) {
            return view('nursing.create');
        }
        abort(404, 'View nursing.create not found.');
    }

    /**
     * On Admission Page
     */
    public function Onaddmission()
    {
        if (view()->exists('nursing.onaddmission')) {
            return view('nursing.onaddmission');
        }
        abort(404, 'View nursing.onaddmission not found.');
    }

    /**
     * Fresh Nursing Page
     */
    public function Fresh()
    {
        if (view()->exists('nursing.fresh')) {
            return view('nursing.fresh');
        }
        abort(404, 'View nursing.fresh not found.');
    }

    /**
     * Pre-Surgery Page
     */
    public function Presurgery()
    {
        if (view()->exists('nursing.presurgery')) {
            return view('nursing.presurgery');
        }
        abort(404, 'View nursing.presurgery not found.');
    }

    /**
     * Post-Surgery Page
     */
    public function PostSurgery()
    {
        if (view()->exists('nursing.postsurgery')) {
            return view('nursing.postsurgery');
        }
        abort(404, 'View nursing.postsurgery not found.');
    }

    /**
     * Round Prescription Page
     */
    public function Roundprescription()
    {
        if (view()->exists('nursing.roundprescription')) {
            return view('nursing.roundprescription');
        }
        abort(404, 'View nursing.roundprescription not found.');
    }

    /**
     * Discharge Page
     */
    public function Discharge()
    {
        if (view()->exists('nursing.discharge')) {
            return view('nursing.discharge');
        }
        abort(404, 'View nursing.discharge not found.');
    }

    /**
     * Release Patients Page
     */
    public function Releasepatients()
    {
        if (view()->exists('nursing.releasepatients')) {
            return view('nursing.releasepatients');
        }
        abort(404, 'View nursing.releasepatients not found.');
    }
}