<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\CoinPair;
use App\Lib\CurlRequest;
use App\Models\CowCurrency;
use App\Models\CowHistories;
use App\Models\Currency;
use App\Models\Trending;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\Page;
use App\Models\Subscriber;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    public function index()
    {
        $reference = @$_GET['reference'];
        if ($reference) {
            session()->put('reference', $reference);
        }

        $pageTitle   = 'Home';
        $sections    = Page::where('tempname', activeTemplate())->where('slug', '/')->first();
        $seoContents = $sections->seo_content;
        $seoImage    = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::home', compact('pageTitle', 'sections', 'seoContents', 'seoImage'));
    }

    public function pages($slug)
    {
        $page = Page::where('tempname', activeTemplate())->where('slug', $slug)->firstOrFail();
        $pageTitle = $page->name;
        $sections = $page->secs;
        $seoContents = $page->seo_content;
        $seoImage = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::pages', compact('pageTitle', 'sections', 'seoContents', 'seoImage'));
    }


    public function contact()
    {
        $pageTitle   = "Contact Us";
        $user        = auth()->user();
        $sections    = Page::where('tempname', activeTemplate())->where('slug', 'contact')->first();
        $seoContents = $sections->seo_content;
        $seoImage    = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::contact', compact('pageTitle', 'user', 'sections', 'seoContents', 'seoImage'));
    }

    public function trending(Request $request)
    {
        $pageTitle = 'Trending';
        $sections = Page::where('tempname', activeTemplate())->where('slug', 'trending')->first();
        $query = Trending::active()->rankOrdering()
            ->searchable(['name', 'symbol']);

        $total = (clone $query)->count();
        $currencies = (clone $query)->skip($request->skip ?? 0)
            ->take($request->limit ?? 50)
            ->get();
        $trendingx = $this->getValueTrending($currencies);
        $defaultActive = $currencies->first();

        return view('Template::trending', compact('pageTitle', 'sections', 'currencies', 'total', 'defaultActive'));
    }

    public function getValueTrending($currencies)
    {
        foreach ($currencies as $currency) {
            if ($currency->type == Status::TRENDINGTYPE_COW) {
                $query = CowCurrency::select(['timestamp', 'rate'])->TimeOrdering()->searchable(['name', 'symbol'])->get();
                $currency->rate = $query;
            } elseif ($currency->type == Status::TRENDINGTYPE_CRYPTO) {
                $url = "https://api.binance.com/api/v3/klines?symbol=" . strtoupper($currency->symbol) . "USDT&interval=1d&limit=100";
                $response = CurlRequest::curlContent($url);
                $array = json_decode($response, true);

                $currency->rate = $array;
            } else {
                if ($currency->symbol == 'XAU')
                    $url = 'https://static.dwcdn.net/data/q7hEo.csv';
                else if ($currency->symbol == 'XAG')
                    $url = 'https://static.dwcdn.net/data/xbqP6.csv';
                $array = $this->getPriceMetal($url);
                $currency->rate = json_decode($array, true);
            }
        }
        return $currencies;
    }



    private function getPriceMetal($url)
    {

        $csv = file_get_contents($url);

        $lines = explode(PHP_EOL, $csv);
        $headers = str_getcsv(array_shift($lines));

        $data = array();
        foreach ($lines as $line) {
            if (!empty($line)) {
                $row = array();
                $fields = str_getcsv($line);
                foreach ($headers as $i => $header) {
                    $row[$header] = $fields[$i];
                }
                $data[] = $row;
            }
        }
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name'    => 'required',
            'email'   => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $random = getNumber();

        $ticket           = new SupportTicket();
        $ticket->user_id  = auth()->id() ?? 0;
        $ticket->name     = $request->name;
        $ticket->email    = $request->email;
        $ticket->priority = Status::PRIORITY_MEDIUM;


        $ticket->ticket     = $random;
        $ticket->subject    = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status     = Status::TICKET_OPEN;
        $ticket->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title     = 'A new contact message has been submitted';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message                    = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message           = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function policyPages($slug)
    {
        $policy = Frontend::where('slug', $slug)->where('data_keys', 'policy_pages.element')->firstOrFail();
        $pageTitle = $policy->data_values->title;
        $seoContents = $policy->seo_content;
        $seoImage = @$seoContents->image ? frontendImage('policy_pages', $seoContents->image, getFileSize('seo'), true) : null;
        return view('Template::policy', compact('policy', 'pageTitle', 'seoContents', 'seoImage'));
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) $lang = 'en';
        session()->put('lang', $lang);
        return back();
    }

    public function blogDetails($slug)
    {
        $blog        = Frontend::where('slug', $slug)->where('data_keys', 'blog.element')->firstOrFail();
        $pageTitle   = $blog->data_values->title;
        $seoContents = $blog->seo_content;
        $seoImage    = @$seoContents->image ? frontendImage('blog', $seoContents->image, getFileSize('seo'), true) : null;
        return view('Template::blog_details', compact('blog', 'pageTitle', 'seoContents', 'seoImage'));
    }


    public function cookieAccept()
    {
        Cookie::queue('gdpr_cookie', gs('site_name'), 43200);
    }

    public function cookiePolicy()
    {
        $cookieContent = Frontend::where('data_keys', 'cookie.data')->first();
        abort_if($cookieContent->data_values->status != Status::ENABLE, 404);
        $pageTitle = 'Cookie Policy';
        $cookie = Frontend::where('data_keys', 'cookie.data')->first();
        return view('Template::cookie', compact('pageTitle', 'cookie'));
    }

    public function placeholderImage($size = null)
    {
        $imgWidth = explode('x', $size)[0];
        $imgHeight = explode('x', $size)[1];
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font/solaimanLipi_bold.ttf');
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance()
    {
        $pageTitle = 'Maintenance Mode';
        if (gs('maintenance_mode') == Status::DISABLE) {
            return to_route('home');
        }
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->first();
        return view('Template::maintenance', compact('pageTitle', 'maintenance'));
    }

    public function pusherAuthentication($socketId, $channelName)
    {
        $general = gs();
        $pusherSecret = @$general->pusher_config->pusher_app_secret;
        $str          = $socketId . ":" . $channelName;
        $hash         = hash_hmac('sha256', $str, $pusherSecret);

        return response()->json([
            'success' => true,
            'message' => "Pusher authentication successfully",
            'auth'    => @$general->pusher_config->pusher_app_key . ":" . $hash,
        ]);
    }

    public function market()
    {
        $pageTitle = 'Market List';
        $sections  = Page::where('tempname', activeTemplate())->where('slug', 'market')->first();
        return view('Template::market_list', compact('pageTitle', 'sections'));
    }
    public function crypto()
    {
        $pageTitle = 'Cryptocurrency';
        $sections  = Page::where('tempname', activeTemplate())->where('slug', 'crypto-currency')->first();
        return view('Template::crypto_currency', compact('pageTitle', 'sections'));
    }

    public function marketList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:all,crypto,fiat',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->all(),
            ]);
        }

        $query = CoinPair::searchable(['symbol'])->select('id', 'market_id', 'coin_id', 'symbol');

        if ($request->type != 'all') {
            $query->whereHas('market', function ($q) use ($request) {
                $q->whereHas('currency', function ($c) use ($request) {
                    if ($request->type == 'crypto') {
                        return $c->crypto();
                    }
                    $c->fiat();
                });
            });
        }

        $query = $query->with('market:id,name,currency_id', 'coin:id,name,symbol,image', 'market.currency:id,name,symbol,image', 'marketData')
            ->withCount('trade as total_trade')
            ->orderBy('total_trade', 'desc');

        $total = (clone $query)->count();
        $pairs = (clone $query)->skip($request->skip ?? 0)
            ->take($request->limit ?? 20)
            ->get();

        return response()->json([
            'success' => true,
            'pairs'   => $pairs,
            'total'   => $total,
        ]);
    }


    public function trendingList(Request $request)
    {
        $query = Trending::active()->with('marketData')->rankOrdering()
            ->searchable(['name', 'symbol']);

        $total      = (clone $query)->count();
        $currencies = (clone $query)->skip($request->skip ?? 0)
            ->take($request->limit ?? 50)
            ->get();

        return response()->json([
            'success'    => true,
            'currencies' => $currencies,
            'total'      => $total,
        ]);
    }

    public function cryptoCurrencyList(Request $request)
    {
        $query = Currency::active()->crypto()->with('marketData')->rankOrdering()
            ->searchable(['name', 'symbol']);

        $total      = (clone $query)->count();
        $currencies = (clone $query)->skip($request->skip ?? 0)
            ->take($request->limit ?? 20)
            ->get();

        return response()->json([
            'success'    => true,
            'currencies' => $currencies,
            'total'      => $total,
        ]);
    }


    public function cowList(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'date' => 'required|in:all,crypto,fiat',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => $validator->errors()->all(),
        //     ]);
        // }

        $dateTime = now();
        $formattedRequestDate = Carbon::parse($request->date)->format('Y-m-d');
        $formattedDateTime = Carbon::parse($dateTime)->format('Y-m-d');

        $priceFiat = defaultCurrencyDataProvider()->getPriceFiat();
        if ($formattedRequestDate === $formattedDateTime) {
            $query      = Currency::active()->cow()->orderByRaw('symbol ASC')->searchable(['name', 'symbol']);
            $total      = (clone $query)->count();
            $currencies = (clone $query)->get();

            // foreach ($currencies as $currency) {
            //     if ($request->lang == "VND" && isset($currency->rate, $priceFiat['rates']['VND'])) {
            //         $currency->rate = (float) $currency->rate * $priceFiat['rates']['VND'];
            //     } else {
            //         $currency->rate = $currency->rate;
            //     }
            // }
        } else {
            $query      = CowHistories::whereDate('time', '=', $formattedRequestDate)->orderByRaw('symbol ASC')->searchable(['name', 'symbol']);
            $total      = (clone $query)->count();
            $currenciesHistories = (clone $query)->get();
            if (!$currenciesHistories) {
                foreach ($currenciesHistories as $currency) {
                    $currencyhis = Currency::where('type', Status::FIAT_CURRENCY)->where('id', $currency->currency_id)->first();

                    $currencies[] = [
                        'id'          => $currency->id,
                        'name'        => $currencyhis->name,
                        'symbol'      => $currency->symbol,
                        'rate'        => $currency->price, //($request->lang == "VND") ? (float) $currency->price * $priceFiat['rates']['VND'] : 
                        'time'        => $currency->time,
                        'basicunit'   => $currencyhis->basicunit,
                        'minorSingle' => $currencyhis->minorSingle,
                        'created_at'  => $currency->created_at,
                        'updated_at'  => $currency->updated_at,
                    ];
                }
            }
        }
        return response()->json([
            'success'    => true,
            'currencies' => $currencies,
            'cow'        => ($formattedRequestDate === $formattedDateTime) ? $currencies->avg('rate') : $currenciesHistories->avg('price'),
            'total'      => $total,
        ]);
    }

    public function pwaConfiguration()
    {
        $gs = gs();
        $json = [
            "name"             => $gs->site_name,
            "sign"             => $gs->site_name,
            "start_url"        => route('trade'),
            "display"          => "standalone",
            "background_color" => "#5900b3",
            "theme_color"      => "black",
            "description"      => $gs->site_name . " PWA",
            "icons"            => [
                [
                    "src"   => getImage(getFilePath('logo_icon') . '/pwa_favicon.png'),
                    "sizes" => "192x192",
                    "type"  => "image/png",
                ],
                [
                    "src"   => getImage(getFilePath('logo_icon') . '/pwa_thumb.png'),
                    "sizes" => "512x512",
                    "type"  => "image/png",
                ],
            ],
        ];

        return response()->json($json);
    }
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:subscribers,email',
        ], [
            'email.unique' => "You have already subscribed",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'   => $validator->errors()->all(),
                'success' => false,
            ]);
        }

        $subscribe        = new Subscriber();
        $subscribe->email = $request->email;
        $subscribe->save();

        return response()->json([
            'message' => "Thank you for subscribing us",
            'success' => true,
        ]);
    }


    public function about()
    {
        $pageTitle = "About Us";
        $sections  = Page::where('tempname', activeTemplate())->where('slug', 'about-us')->firstOrFail();
        return view('Template::about', compact('pageTitle', 'sections'));
    }
}
