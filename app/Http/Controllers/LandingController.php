<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteService;
use App\Models\ProcessStep;
use App\Models\WhyCard;
use App\Models\ContactInfo;
use App\Models\SocialLink;
use App\Models\PageSection;

class LandingController extends Controller
{
    public function index()
    {
        $services = SiteService::activos()->orderBy('sort_order')->get();
        $steps = ProcessStep::activos()->orderBy('sort_order')->get();
        $whyCards = WhyCard::activos()->orderBy('sort_order')->get();
        $socialLinks = SocialLink::activos()->orderBy('sort_order')->get();

        $allContacts = ContactInfo::activos()->orderBy('sort_order')->get();
        $contacts = $allContacts->keyBy('type')->toArray();

        // Load CMS sections with their images
        $sections = PageSection::with('mediaItems.media')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('key');

        $whatsappNumber = '51943694464';
        if (isset($contacts['whatsapp']['value'])) {
            $whatsappNumber = $contacts['whatsapp']['value'];
        }
        $whatsappLink = "https://wa.me/{$whatsappNumber}";

        return view('landing-page', compact(
            'services', 'steps', 'whyCards', 'socialLinks',
            'contacts', 'whatsappNumber', 'whatsappLink', 'sections'
        ));
    }
}
