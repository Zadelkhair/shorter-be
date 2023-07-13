<?php

namespace App\Http\Controllers;

use App\Models\Url;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Torann\GeoIP\Facades\GeoIP;

class UrlController extends Controller
{
    // shorten URL
    public function shorten(Request $request)
    {
        // validate URL
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'duration' => 'integer|min:1' // assuming duration is in minutes
        ]);

        // if validation fails
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 422);
        }

        // generate short URL
        $shortUrl = $this->generateShortUrl();

        // create new URL record
        $url = new Url();
        $url->url = $request->url;
        $url->short_url = $shortUrl;
        $url->duration = $request->duration ?: 48 * 60 * 60; // 48 hours

        // if user is logged in
        if (auth()->user()) {
            $url->user_id = auth()->user()->id;
        }
        else if ($request->nonloggedinuser_id) {
            // if user is not logged in
            $url->nonloggedinuser_id = $request->nonloggedinuser_id;
        }

        // create
        $url->save();

        return $this->apiResponse(['url' => $url], null, 201);
    }

    // generate a random short URL
    private function generateShortUrl($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $shortUrl = '';

        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $shortUrl .= $characters[$index];
        }

        return $shortUrl;
    }

    // get all URLs
    public function index(Request $request)
    {

        // if not logged in
        if (!auth()->user()) {
            // if request contains a nonloggedinuser_id
            if ($request->nonloggedinuser_id) {

                $urls = Url::where('nonloggedinuser_id', $request->nonloggedinuser_id);

                // with count of visites
                $urls = $urls->withCount('visits');

                // if request contains a search query
                if ($request->search) {
                    $urls = $urls->where('url', 'LIKE', '%' . $request->search . '%');
                    $urls = $urls->orWhere('short_url', 'LIKE', '%' . $request->search . '%');
                }

                // order by created at
                $urls = $urls->orderBy('created_at', 'desc');

                // paginate
                $urls = $urls->paginate(10);

                return $this->apiResponse(['urls' => $urls], null, 200);
            }

            return $this->apiResponse(null, 'Unauthorized', 403);
        }

        // dump role

        // if an admin
        if (auth()->user()->hasRole('admin')) {
            // get url with visits
            $urls = Url::withCount('visits');

            // if request contains a search query
            if ($request->search) {
                $urls = $urls->where('url', 'LIKE', '%' . $request->search . '%');
                $urls = $urls->orWhere('short_url', 'LIKE', '%' . $request->search . '%');
            }

            // order by created at
            $urls = $urls->orderBy('created_at', 'desc');

            // paginate
            $urls = $urls->paginate(10);

            return $this->apiResponse(['urls' => $urls], null, 200);
        }

        $urls = auth()->user()->urls();

        // with count of visites
        $urls = $urls->withCount('visits');

        // if request contains a search query
        if ($request->search) {
            $urls = $urls->where('url', 'LIKE', '%' . $request->search . '%');
            $urls = $urls->orWhere('short_url', 'LIKE', '%' . $request->search . '%');
        }

        // order by created at
        $urls = $urls->orderBy('created_at', 'desc');

        // paginate
        $urls = $urls->paginate(10);

        return $this->apiResponse(['urls' => $urls], null, 200);
    }

    // get views paginaton
    public function views(Request $request, $id)
    {
        $url = Url::find($id);

        if (!$url) {
            return $this->apiResponse(null, 'URL not found', 404);
        }

        // check if the request contains nonloggedinuser_id
        if ($request->nonloggedinuser_id) {
            // check if the url belongs to the nonloggedinuser
            if ($url->nonloggedinuser_id != $request->nonloggedinuser_id) {
                return $this->apiResponse(null, 'Unauthorized', 403);
            }
        }
        else if ($url->user_id != auth()->user()->id && !auth()->user()->hasRole('admin')) {
            return $this->apiResponse(null, 'Unauthorized', 403);
        }

        // get visits
        $visits = $url->visits();

        // if request contains a search query
        if ($request->search) {
            $visits = $visits->where('ip', 'LIKE', '%' . $request->search . '%');
            $visits = $visits->orWhere('country', 'LIKE', '%' . $request->search . '%');
            $visits = $visits->orWhere('city', 'LIKE', '%' . $request->search . '%');
            $visits = $visits->orWhere('device', 'LIKE', '%' . $request->search . '%');
            $visits = $visits->orWhere('browser', 'LIKE', '%' . $request->search . '%');
            $visits = $visits->orWhere('platform', 'LIKE', '%' . $request->search . '%');
        }

        // order by created at
        $visits = $visits->orderBy('created_at', 'desc');

        // paginate
        $visits = $visits->paginate(10);

        return $this->apiResponse(['visits' => $visits], null, 200);
    }

    // delete URL
    public function destroy(Request $request,$id)
    {
        $url = Url::find($id);

        if (!$url) {
            return $this->apiResponse(null, 'URL not found', 404);
        }

        // check if the request contains nonloggedinuser_id
        if ($request->nonloggedinuser_id) {
            // check if the url belongs to the nonloggedinuser
            if ($url->nonloggedinuser_id != $request->nonloggedinuser_id) {
                return $this->apiResponse(null, 'Unauthorized', 403);
            }
        }
        else if ($url->user_id != auth()->user()->id && !auth()->user()->hasRole('admin')) {
            return $this->apiResponse(null, 'Unauthorized', 403);
        }

        // delete visits
        $url->visits()->delete();
        $url->delete();

        return $this->apiResponse(null, null, 200);
    }

    // update URL
    public function update(Request $request, $id)
    {

        // validate URL
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'duration' => 'integer|min:1' // assuming duration is in minutes
        ]);

        // if validation fails
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 422);
        }

        $url = Url::find($id);

        if (!$url) {
            return $this->apiResponse(null, 'URL not found', 404);
        }

        // check if user can update the url
        if ($url->user_id != auth()->user()->id && !auth()->user()->hasRole('admin')) {
            return $this->apiResponse(null, 'Unauthorized', 403);
        }

        $url->url = $request->url;
        $url->duration = $request->duration ?: 24*60*60; // set default duration to 48 hours if not provided
        $url->save();

        return $this->apiResponse(['url' => $url], null, 200);
    }

    // get URL (everyone)
    public function show($id)
    {

        $url = Url::find($id);

        if (!$url) {
            return $this->apiResponse(null, 'URL not found', 404);
        }

        return $this->apiResponse(['url' => $url], null, 200);
    }

    // get user URLs ( only admins can use this )
    public function userUrls($id)
    {
        // check if the authenticated user is an admin
        if (!auth()->user()->hasRole('admin')) {
            return $this->apiResponse(null, 'Unauthorized', 403);
        }

        // with visits
        $urls = Url::where('user_id', $id)->with('visits')->get();
        return $this->apiResponse(['urls' => $urls], null, 200);
    }

    // visit
    public function visit(Request $request, $shortUrl)
    {
        $url = Url::where('short_url', $shortUrl)->first();

        if (!$url) {
            return $this->apiResponse(null, 'URL not found', 404);
        }

        // Check if the URL has expired
        if ($url->created_at->addMinutes($url->duration) < now()) {
            return $this->apiResponse(null, 'URL has expired', 403);
        }

        // Increment clicks
        $url->clicks++;
        $url->save();

        // Get geolocation information
        $ip = $request->ip();
        $location = GeoIP::getLocation($ip);
        $country = $location->country;
        $city = $location->city;
        $countryCode = $location->iso_code;

        // Get browser information
        $browser = $request->header('User-Agent');

        // Get device information
        $device = $request->header('User-Agent');

        // Create visit
        Visit::create([
            'url_id' => $url->id,
            'ip' => $ip,
            'country' => $country . ' (' . $countryCode . ')',
            'city' => $city,
            'browser' => $browser,
            'device' => $device,
        ]);

        return redirect($url->url);
    }

}
