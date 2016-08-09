<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Mineral;
use App\MineralsImage;
use Illuminate\Http\Request;

class MineralsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $minerals = Mineral::with(['mineralsImages' => function ($query) {
            $query->where('main_image_of_mineral', true);
        }, 'user'])->paginate(10);

        return view('minerals/index', [
            'minerals' => $minerals,
        ]);
    }

    public function show(Request $request, $id)
    {
        // получаем минерал
        $mineral = Mineral::with('user', 'mineralsImages', 'lastUpdater')->where('id', $id)->first();
        if (is_null($mineral)) {
            return redirect('/')->withErrors(['Такого минерала у нас нет.'])->setStatusCode(404);
        }
        return view('minerals/show', [
            'mineral' => $mineral
        ]);
    }

    public function getCreate(Request $request)
    {
        if (!$request->user()->is('admin|moderator|editor')) {
            return redirect('/')->withErrors(['У вас нет прав доступа к этой странице'])->setStatusCode(403);
        }
        return view('minerals/create');
    }

    public function postCreate(Request $request)
    {
        if (is_null($request->user()) OR !$request->user()->is('admin|moderator|editor')) {
            return redirect('/')->withErrors(['У вас нет прав доступа к этой странице'])->setStatusCode(403);
        }
        $this->validate($request, [
            'images_ids' => ['array', 'max:30'],
            'main_image_id' => ['integer', 'min:1'],

            'name' => ['required', 'string', 'max:255', 'unique:minerals,name'],
            'description' => ['string', 'max:4000'],
            'class' => ['string', 'max:255'],
            'hardness_before' => 'custom_numeric:0,10',
            'hardness_after' => 'custom_numeric:0,10',
            'color' => 'string|max:255',
            'color_in_line' => 'string|max:255',
            'transparency' => 'string|max:255',
            'density_before' => 'custom_numeric:0,3000',
            'density_after' => 'custom_numeric:0,3000',
            'shine' => 'string|max:255',
            'cleavage' => 'string|max:255',
            'fracture' => 'string|max:255',
            'genesis' => 'string|max:255',
            'practical_use' => 'string|max:2000',
            'chemical_formula' => 'string|max:255',
            'deposit' => 'string|max:2000',
            'seen' => 'boolean',
        ], [
            'name.unique' => 'Минерал с таким названием уже есть.',
        ]);

        mb_internal_encoding("UTF-8");
        function mb_ucfirst($text)
        {
            return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
        }

        $ar_data = [
            'name' => mb_ucfirst($request->name),
            'description' => mb_ucfirst($request->description),
            'class' => mb_ucfirst($request->class),
            'hardness_before' => $request->hardness_before,
            'hardness_after' => $request->hardness_after,
            'color' => mb_ucfirst($request->color),
            'color_in_line' => mb_ucfirst($request->color_in_line),
            'transparency' => mb_ucfirst($request->transparency),
            'density_before' => $request->density_before,
            'density_after' => $request->density_after,
            'shine' => mb_ucfirst($request->shine),
            'cleavage' => mb_ucfirst($request->cleavage),
            'fracture' => mb_ucfirst($request->fracture),
            'genesis' => mb_ucfirst($request->genesis),
            'practical_use' => mb_ucfirst($request->practical_use),
            'chemical_formula' => $request->chemical_formula,
            'deposit' => mb_ucfirst($request->deposit),
            'views' => 0,
        ];

        if ($request->has('seen') AND $request->user()->is('admin|moderator')) {
            $ar_data['seen'] = (bool)$request->seen;
        } else {
            $ar_data['seen'] = false;
        }

        $mineral = $request->user()->minerals()->create($ar_data);

        $images_ids = $request->images_ids;
        $main_image_id = (int)$request->main_image_id;
        if (is_array($images_ids) AND !empty($images_ids)) {
            $checked_images_ids = [];
            foreach ($images_ids as $id) {
                if ((int)$id >= 1) {
                    $checked_images_ids[] = (int)$id;
                }
            }
            $checked_images_ids = array_unique($checked_images_ids);
            if (!empty($checked_images_ids)) {
                try {
                    MineralsImage::whereIn('id', $checked_images_ids)->where('mineral_id', null)->update(['mineral_id' => $mineral->id]);
                    $db_images = MineralsImage::where('mineral_id', $mineral->id)->get();
                    $checked_images_ids = [];
                    foreach ($db_images as $image) {
                        $checked_images_ids[] = (int)$image->id;
                    }
                    if (!($main_image_id >= 1) OR !in_array($main_image_id, $checked_images_ids, true)) {
                        $main_image_id = $checked_images_ids[array_rand($checked_images_ids)];
                    }
                    MineralsImage::where('id', $main_image_id)->update(['main_image_of_mineral' => true]);
                } catch (\Exception $e) {
                    return redirect('/minerals/create')->withSuccess(['Минерал добавлен.'])->withError(['Прикрепить изображения к минералу или назначить главное изображение не вышло.'])->withInfo(['<a href="' . \Config::get('app.url') . request()->route()->getPrefix() . '/' . $mineral->id . '" class="alert-link">Перейти</a> к добавленному минералу.']);
                }
            }
        }

        return redirect('/minerals/create')->withSuccess(['Минерал добавлен.'])->withInfo(['<a href="' . \Config::get('app.url') . request()->route()->getPrefix() . '/' . $mineral->id . '" class="alert-link">Перейти к добавленному минералу.</a>']);
    }

    public function getUpdate(Request $request, $id)
    {
        if (is_null($request->user()) OR !$request->user()->is('admin|moderator|editor')) {
            return redirect('/')->withErrors(['У вас нет прав доступа к этой странице.'])->setStatusCode(403);
        }
        // получаем минерал
        $mineral = Mineral::with('mineralsImages')->where('id', $id)->first();
        if (is_null($mineral)) {
            return redirect('/')->withErrors(['Такого минерала у нас нет.'])->setStatusCode(404);
        }
        // еще одна проверка
        if ($request->user()->is('editor')) {
            // текущий пользователь автор этой записи?
            if ($mineral->user_id !== $request->user()->id) {
                return redirect('/')->withErrors(['У вас нет прав доступа к этой странице.'])->setStatusCode(403);
            }
        }
        return view('minerals/update', [
            'mineral' => $mineral
        ]);
    }

    public function postUpdate(Request $request, $id)
    {
        if (is_null($request->user()) OR !$request->user()->is('admin|moderator|editor')) {
            return redirect('/')->withErrors(['У вас нет прав доступа к этой странице.'])->setStatusCode(403);
        }
        // получаем минерал
        $mineral = Mineral::where('id', $id)->first();
        if (is_null($mineral)) {
            return redirect('/')->withErrors(['Такого минерала у нас нет.'])->setStatusCode(404);
        }
        // еще одна проверка
        if ($request->user()->is('editor')) {
            // если роль editor, то такой пользователь может редактировать только свои записи о минералах
            if ($mineral->user_id !== $request->user()->id) {
                return redirect('/')->withErrors(['У вас нет прав доступа к этой странице.'])->setStatusCode(403);
            }
        }

        // validate
        $this->validate($request, [
            'images_ids' => ['array', 'max:30'],
            'main_image_id' => ['integer', 'min:1'],

            'name' => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:4000'],
            'class' => ['string', 'max:255'],
            'hardness_before' => 'custom_numeric:0,10',
            'hardness_after' => 'custom_numeric:0,10',
            'color' => 'string|max:255',
            'color_in_line' => 'string|max:255',
            'transparency' => 'string|max:255',
            'density_before' => 'custom_numeric:0,3000',
            'density_after' => 'custom_numeric:0,3000',
            'shine' => 'string|max:255',
            'cleavage' => 'string|max:255',
            'fracture' => 'string|max:255',
            'genesis' => 'string|max:255',
            'practical_use' => 'string|max:2000',
            'chemical_formula' => 'string|max:255',
            'deposit' => 'string|max:2000',
            'seen' => 'boolean',
        ]);

        mb_internal_encoding("UTF-8");
        function mb_ucfirst($text)
        {
            return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
        }

        $ar_data = [
            'name' => mb_ucfirst($request->name),
            'description' => mb_ucfirst($request->description),
            'class' => mb_ucfirst($request->class),
            'hardness_before' => $request->hardness_before,
            'hardness_after' => $request->hardness_after,
            'color' => mb_ucfirst($request->color),
            'color_in_line' => mb_ucfirst($request->color_in_line),
            'transparency' => mb_ucfirst($request->transparency),
            'density_before' => $request->density_before,
            'density_after' => $request->density_after,
            'shine' => mb_ucfirst($request->shine),
            'cleavage' => mb_ucfirst($request->cleavage),
            'fracture' => mb_ucfirst($request->fracture),
            'genesis' => mb_ucfirst($request->genesis),
            'practical_use' => mb_ucfirst($request->practical_use),
            'chemical_formula' => $request->chemical_formula,
            'deposit' => mb_ucfirst($request->deposit),
            'last_updater_id' => $request->user()->id,
        ];

        if ($request->has('seen') AND $request->user()->is('admin|moderator')) {
            $ar_data['seen'] = (bool)$request->seen;
        } else {
            $ar_data['seen'] = false;
        }

        $dubl_name_min = Mineral::where('name', $ar_data['name'])->where('id', '!=', $mineral->id)->first();
        if ($dubl_name_min) {
            return back()->withErrors(['Минерал с таким именем уже есть. <a class="alert-link" href="/minerals/' . $dubl_name_min->id . '">Перейдите, чтобы увидеть его.</a>'])->withInput();
        }

        $mineral->update($ar_data);

        // TODO сделать так:
        // отвязываю все ранее привязанные изображения
        // убрать везде пометку, что изображение главное
        // привязываю переданные
        MineralsImage::where('mineral_id', $mineral->id)->update(['main_image_of_mineral' => false, 'mineral_id' => null]);

        $images_ids = $request->images_ids;
        $main_image_id = (int)$request->main_image_id;
        if (is_array($images_ids) AND !empty($images_ids)) {
            $checked_images_ids = [];
            foreach ($images_ids as $id) {
                if ((int)$id >= 1) {
                    $checked_images_ids[] = (int)$id;
                }
            }
            $checked_images_ids = array_unique($checked_images_ids);
            if (!empty($checked_images_ids)) {
                try {
                    MineralsImage::whereIn('id', $checked_images_ids)->where('mineral_id', null)->update(['mineral_id' => $mineral->id]);
                    $db_images = MineralsImage::where('mineral_id', $mineral->id)->get();
                    $checked_images_ids = [];
                    foreach ($db_images as $image) {
                        $checked_images_ids[] = (int)$image->id;
                    }
                    if (!($main_image_id >= 1) OR !in_array($main_image_id, $checked_images_ids, true)) {
                        $main_image_id = $checked_images_ids[array_rand($checked_images_ids)];
                    }
                    MineralsImage::where('id', $main_image_id)->update(['main_image_of_mineral' => true]);
                } catch (\Exception $e) {
                    return back()->withSuccess(['Информация о минерале обновлена.'])->withError(['Прикрепить изображения к минералу или назначить главное изображение не вышло.'])->withInfo(['<a href="' . \Config::get('app.url') . request()->route()->getPrefix() . '/' . $mineral->id . '" class="alert-link">Перейтик обновлённому минералу.</a>']);
                }
            }
        }

        return back()->withSuccess(['Информация о минерале обновлена.'])->withInfo(['<a href="' . \Config::get('app.url') . request()->route()->getPrefix() . '/' . $mineral->id . '" class="alert-link">Перейти к обновлённому минералу.</a>']);
    }

    //public function post

    public function postAutocomplete(Request $request)
    {
        if (is_null($request->user()) OR !$request->user()->is('admin|moderator|editor')) {
            return response()->json(['success' => 0, 'msgs' => ['error' => ['У вас нет прав доступа к этой странице.'], 'success' => [], 'warning' => [], 'info' => []]], 403);
        }

        $validator = \Validator::make($request->all(), [
            'field' => 'required|string|in:name,class,color,color_in_line,transparency,shine,cleavage,fracture,genesis',
            'term' => 'required|string|min:2|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'msgs' => ['error' => $validator->errors()->all(), 'success' => [], 'warning' => [], 'info' => []]], 406);
        }

        $field = $request->field;

        $result = Mineral::where($field, 'like', $request->term . '%')->take(6)->orderBy($field)->distinct()->get([$field]);

        $ar_resp = [];
        foreach ($result as $v) {
            $ar_resp[] = ['value' => $v[$field], 'label' => $v[$field]];
        }

        return response()->json(['success' => 1, 'response' => $ar_resp, 'msgs' => ['error' => [], 'success' => [], 'warning' => [], 'info' => []]], 200);
    }
}