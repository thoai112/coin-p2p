<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Models\Currency;
use App\Models\CowCurrency;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;
use App\Models\MarketData;
use App\Models\Trending;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    public function crypto()
    {
        $pageTitle            = "Crypto Currency List";
        $currencies           = $this->currencyData('crypto');
        $type                 = Status::CRYPTO_CURRENCY;
        $currencyDataProvider = defaultCurrencyDataProvider(false);
        return view('admin.currency.list', compact('pageTitle', 'currencies', 'type', 'currencyDataProvider'));
    }

    public function fiat()
    {
        $pageTitle            = "Fiat Currency List";
        $currencies           = $this->currencyData('fiat');
        $type                 = Status::FIAT_CURRENCY;
        $currencyDataProvider = defaultCurrencyDataProvider(false);
        return view('admin.currency.list', compact('pageTitle', 'currencies', 'type', 'currencyDataProvider'));
    }

    public function cow()
    {
        $pageTitle            = "Cow Currency History";
        $currencies           = $this->cowData('cow');
        $type                 = Status::COW_CURRENCY;
        $cow                  = $this->currencyData('cow');
        $avgcow                 = $cow->avg('rate');
        $currencyDataProvider = defaultCurrencyDataProvider(false);
        return view('admin.currency.list', compact('pageTitle', 'currencies', 'type', 'avgcow', 'currencyDataProvider'));
    }

    public function toptrending()
    {
        $pageTitle            = "Top Trending List";
        $currencies           = $this->trendingData('trending');
        $type                 = Status::TRENDING;
        $currencyDataProvider = defaultCurrencyDataProvider(false);
        return view('admin.currency.list', compact('pageTitle', 'currencies', 'type', 'currencyDataProvider'));
    }

    private function trendingData($scope = null)
    {
        $query = Trending::query();
        if ($scope) {
            $query->$scope();
        }
        if ($scope == 'trending') {
            $query->rankOrdering();
        }
        return $query->with('marketData')->searchable(['name', 'symbol', 'ranking'])->paginate(getPaginate());
    }

    private function currencyData($scope = null)
    {
        $query = Currency::query();
        if ($scope) {
            $query->$scope();
        }
        if ($scope == 'crypto') {
            $query->rankOrdering();
        }
        if ($scope == 'fiat') {
            $query->symbolOrdering();
        }
        return $query->with('marketData')->searchable(['name', 'symbol', 'ranking'])->paginate(getPaginate());
    }
    
    private function cowData($scope = null)
    {
        $query = CowCurrency::query();
        if ($scope) {
            $query->$scope();
        }
        // if ($scope == 'cow') {
        //     $query->rankOrdering();
        // }
        return $query->with('marketData')->searchable(['name', 'symbol', 'ranking'])->orderBy('timestamp', 'ASC')->paginate(getPaginate());
    }


    private function saveCow($scope = null)
    {
        $query = CowCurrency::query();
        if ($scope) {
            $query->$scope();
        }
        if ($scope == 'cow') {
            $query->rankOrdering();
        }
        return $query->with('marketData')->searchable(['name', 'symbol', 'ranking'])->paginate(getPaginate());
    }


    public function save(Request $request, $id = 0)
    {
        $imageValidation = $id ? 'nullable' : 'required';

        $request->validate([
            'name'   => "required|max:255|unique:currencies,name,$id",
            'symbol' => "required|max:40|unique:currencies,symbol,$id",
            'sign'   => "nullable",
            'image'  => ["nullable", 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'type'   => 'required|in:' . Status::FIAT_CURRENCY . ',' . Status::CRYPTO_CURRENCY . '',
            'price'  => 'nullable|numeric|gte:0',
            'p2p_sn' => 'nullable|integer|gte:0',
        ]);

        if ($request->rank && Currency::where('rank', $request->rank)->where('id', '!=', $id)->exists()) {
            return returnBack("Can't be one more currency with the same rank.", 'error', true);
        }

        if ($id) {
            $currency = Currency::findOrFail($id);
            $message  = "Currency updated successfully";
        } else {
            $currency = new Currency();
            $message  = "Currency added successfully";
        }

        $currency->type             = $request->type;
        $currency->name             = $request->name;
        $currency->symbol           = strtoupper($request->symbol);
        $currency->rate             = $request->price;
        $currency->sign             = $request->sign ?? null;
        $currency->p2p_sn           = $request->p2p_sn ?? 0;
        $currency->basicunit        = $request->basicunit ?? 0;
        $currency->minorSingle      = $request->minorSingle ?? null;
        $currency->highlighted_coin = $request->is_highlighted_coin ? Status::YES : Status::NO;
        $currency->iscow            = $request->is_cow ? Status::YES : Status::NO;

        if ($request->hasFile('image')) {
            $path = getFilePath('currency');
            $size = getFileSize('currency');
            try {
                $filename      = fileUploader($request->image, $path, $size, @$currency->image);
                $currency->image = $filename;
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $currency->save();

        if ($currency->type == Status::CRYPTO_CURRENCY) {
            $marketData = MarketData::where('pair_id', 0)->where('currency_id', $currency->id)->first();
            if (!$marketData) {
                $marketData              = new MarketData();
                $marketData->currency_id = $currency->id;
                $marketData->symbol      = $currency->symbol;
                $marketData->pair_id     = 0;
            }
            $marketData->price = $request->price;
            $marketData->save();
        }
        return returnBack($message, 'success');
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start' => 'nullable|integer|gte:1',
            'limit' => 'nullable|integer|gte:1|lte:100',
            'type'  => 'required|in:' . Status::CRYPTO_CURRENCY . ',' . Status::FIAT_CURRENCY . ''
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->all()
            ]);
        }

        $parameters = [
            'start' => $request->start ?? 1,
            'limit' => $request->limit ?? 100,
        ];

        try {
            $import = defaultCurrencyDataProvider()->import($parameters, $request->type);
            return response()->json([
                'success' => true,
                'message' => "$import currencies import successfully"
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage()
            ]);
        }
    }

    public function status($id)
    {
        return Currency::changeStatus($id);
    }

    public function all()
    {
        $query = Currency::active();

        if (request()->type == Status::CRYPTO_CURRENCY) $query->where('type', Status::CRYPTO_CURRENCY)->rankOrdering();
        if (request()->type == Status::FIAT_CURRENCY) $query->where('type', Status::FIAT_CURRENCY)->orderBy('id', 'desc');
        if (request()->search) $query->where(function ($q) {
            $q->where('name', 'like', '%' . request()->search . '%')->orWhere('symbol', 'like', '%' . request()->search . '%');
        });
        $currencies = $query->paginate(getPaginate());

        return response()->json([
            'success'    => true,
            'currencies' => $currencies,
            'more'       => $currencies->hasMorePages()
        ]);
    }


    public function saveCowData(Request $request)
    {
        // $checkDate      = $request;
        // $date = Carbon::parse(trim($checkDate))->format('Y-m-d');
        $parameters = [
            'from' => $request->from ?? 'USD',
            'date' => Carbon::parse(trim($request->date))->format('Y-m-d'),// ?? Carbon::parse(trim(now()))->format('Y-m-d'),
        ];

        try {
            $import = defaultCurrencyDataProvider()->saveCowData($parameters);
            return response()->json([
                'success' => true,
                'message' => "$import currencies import successfully"
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage()
            ]);
        }
    }

    public function updateFiat()
    {
        
        try {
            $import = defaultCurrencyDataProvider()->updateFiat();
            return response()->json([
                'success' => true,
                'data' => $import,
                'message' => "currencies update successfully"
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage()
            ]);
        }
    }
}
