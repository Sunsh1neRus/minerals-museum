<?php

namespace App\Http\Controllers;

use App\Mineral;
use App\MineralsImage;
use File;
use Validator;
use Illuminate\Http\Request;

use App\Http\Requests;
use Image;

class ImagesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUploadMineralImage(Request $request)
    {
        if (is_null($request->user()) OR !$request->user()->is('admin|moderator|editor')) {
            return response()->json(['success' => 0, 'msgs' => ['error' => ['У вас нет прав доступа к этой странице.'], 'success' => [], 'warning' => [], 'info' => []]], 403);
        }

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:10240',
            'mineral_id' => 'integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'msgs' => ['error' => $validator->errors()->all(), 'success' => [], 'warning' => [], 'info' => []]], 406);
        }

        // проверить наличие минерала с этим id
        $mineral = null;
        $description = null;
        if (!is_null($request->mineral_id)) {
            $mineral = Mineral::where('id', $request->mineral_id)->first();
            if (is_null($mineral)) {
                return response()->json(['success' => 0, 'msgs' => ['error' => ['Такого минерала у нас нет.'], 'success' => [], 'warning' => [], 'info' => []]], 404);
            }
            $description = $mineral->name;
        }

        // TODO нужен рефактор

        // расширение
        $ext = $request->file('image')->getClientOriginalExtension();
        // будущий адрес изображения среднего
        $rand_int = rand(1, 5);
        $rand_md5 = md5(str_random(rand(6, 15)));
        $dir = '/images/uploads/minerals/' . $rand_int . '/' . $rand_md5[0] . '/' . $rand_md5[1] . '/';
        // проверить существование папки
        if (!is_dir(public_path() . $dir)) {
            // создаём новую папку
            if (!is_dir(public_path() . '/images/uploads/minerals/' . $rand_int)) {
                mkdir(public_path() . '/images/uploads/minerals/' . $rand_int);
            }
            if (!is_dir(public_path() . '/images/uploads/minerals/' . $rand_int . '/' . $rand_md5[0])) {
                mkdir(public_path() . '/images/uploads/minerals/' . $rand_int . '/' . $rand_md5[0]);
            }
            if (!is_dir(public_path() . '/images/uploads/minerals/' . $rand_int . '/' . $rand_md5[0] . '/' . $rand_md5[1])) {
                mkdir(public_path() . '/images/uploads/minerals/' . $rand_int . '/' . $rand_md5[0] . '/' . $rand_md5[1]);
            }
        }

        // генерация неповторяющегося имени
        do {
            $filename_m_image = str_random(30);
        } while (File::exists(public_path() . $dir . $filename_m_image . '.' . $ext));
        $url_m_image = $dir . $filename_m_image . '.' . $ext;

        // будущий адрес изображения оригинального
        $rand_int = rand(1, 5);
        $rand_md5 = md5(str_random(rand(6, 15)));
        $dir = '/images/uploads/minerals/' . $rand_int . '/' . $rand_md5[0] . '/' . $rand_md5[1] . '/';
        // проверить существование папки
        if (!is_dir(public_path() . $dir)) {
            // создаём новую папку
            if (!is_dir(public_path() . '/images/uploads/minerals/' . $rand_int)) {
                mkdir(public_path() . '/images/uploads/minerals/' . $rand_int);
            }
            if (!is_dir(public_path() . '/images/uploads/minerals/' . $rand_int . '/' . $rand_md5[0])) {
                mkdir(public_path() . '/images/uploads/minerals/' . $rand_int . '/' . $rand_md5[0]);
            }
            if (!is_dir(public_path() . '/images/uploads/minerals/' . $rand_int . '/' . $rand_md5[0] . '/' . $rand_md5[1])) {
                mkdir(public_path() . '/images/uploads/minerals/' . $rand_int . '/' . $rand_md5[0] . '/' . $rand_md5[1]);
            }
        }

        // генерация неповторяющегося имени
        do {
            $filename_o_image = str_random(30);
        } while (File::exists(public_path() . $dir . $filename_o_image . '.' . $ext));
        $url_o_image = $dir . $filename_o_image . '.' . $ext;

        //создание изображения
        $img_o = Image::make($request->file('image'));
        // watermark
        $watermark = Image::make(public_path() . '/images/watermark.png')->resize(round($img_o->width() * 0.1), round($img_o->height() * 0.1), function ($constraint) {
            $constraint->aspectRatio();
        });
        $ar_position = [
            'top-left',
            'top-right',
            'bottom-left',
            'bottom-right',
        ];
        $img_o->insert($watermark, $ar_position[array_rand($ar_position)])->save(public_path() . $url_o_image);
        // создание копии с меньшим разрешением
        Image::make($img_o)->resize(300, 200, function ($constraint) {
            $constraint->aspectRatio();
        })->save(public_path() . $url_m_image);

        try {
            // добавить ссылку на изображение в бд
            $new_image = $request->user()->mineralsImages()->create([
                'mineral_id' => $request->mineral_id,
                'url_original' => $url_o_image,
                'url_middle' => $url_m_image,
                'description' => $description,
            ]);
        } catch (\Exception $e) {
            // откат изменений. Удаление картинки
            File::delete(public_path() . $url_o_image, public_path() . $url_m_image);
            return response()->json(['success' => 0, 'msgs' => ['error' => ['Ошибка при добавлении изображения в БД.'], 'success' => [], 'warning' => [], 'info' => []]], 500);
        }

        return response()->json(['success' => 1, 'url_o_image' => $url_o_image, 'url_m_image' => $url_m_image, 'image_id' => $new_image->id], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDeleteMineralImage(Request $request)
    {
        if (is_null($request->user()) OR !$request->user()->is('admin|moderator|editor')) {
            return response()->json(['success' => 0, 'msgs' => ['error' => ['У вас нет прав доступа к этой странице.'], 'success' => [], 'warning' => [], 'info' => []]], 403);
        }

        $validator = Validator::make($request->all(), [
            'image_id' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'msgs' => ['error' => $validator->errors()->all(), 'success' => [], 'warning' => [], 'info' => []]], 406);
        }

        $image = MineralsImage::where('id', $request->image_id)->first();
        if (is_null($image)) {
            return response()->json(['success' => 0, 'msgs' => ['error' => ['Такого изображения у нас нет.'], 'success' => [], 'warning' => [], 'info' => []]], 406);
        }
        $url_o_image = $image->url_original;
        $url_m_image = $image->url_middle;

        try {
            $image->delete();
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'msgs' => ['error' => ['Внутренняя ошибка сервера.'], 'success' => [], 'warning' => [], 'info' => []]], 500);
        }

        File::delete(public_path() . $url_o_image, public_path() . $url_m_image);
        return response()->json(['success' => 1, 'msgs' => ['error' => [], 'success' => ['Изображение удалено'], 'warning' => [], 'info' => []]], 200);
    }

    public function postUpdateMineralImageDescription(Request $request)
    {
        if (is_null($request->user()) OR !$request->user()->is('admin|moderator|editor')) {
            return response()->json(['success' => 0, 'msgs' => ['error' => ['У вас нет прав доступа к этой странице.'], 'success' => [], 'warning' => [], 'info' => []]], 403);
        }

        $validator = Validator::make($request->all(), [
            'image_id' => 'required|integer|min:1|exists:minerals_images,id',
            'description' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'msgs' => ['error' => $validator->errors()->all(), 'success' => [], 'warning' => [], 'info' => []]], 406);
        }

        $result = MineralsImage::where('id', $request->image_id)->update(['description' => $request->description]);
        if ($result) {
            return response()->json(['success' => 1, 'msgs' => ['error' => [], 'success' => ['Описание изображения изменено.'], 'warning' => [], 'info' => []]], 200);
        } else {
            return response()->json(['success' => 0, 'msgs' => ['error' => ['Ошибка сервера.'], 'success' => [], 'warning' => [], 'info' => []]], 500);
        }
    }

    public function postSetMineralMainImage(Request $request)
    {
        if (is_null($request->user()) OR !$request->user()->is('admin|moderator|editor')) {
            return response()->json(['success' => 0, 'msgs' => ['error' => ['У вас нет прав доступа к этой странице.'], 'success' => [], 'warning' => [], 'info' => []]], 403);
        }

        $validator = Validator::make($request->all(), [
            'image_id' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'msgs' => ['error' => $validator->errors()->all(), 'success' => [], 'warning' => [], 'info' => []]], 406);
        }

        $image = MineralsImage::where('id', $request->image_id)->first();
        if (is_null($image)) {
            return response()->json(['success' => 0, 'msgs' => ['error' => ['Такого изображения у нас нет.'], 'success' => [], 'warning' => [], 'info' => []]], 406);
        }
        if ($image->main_image_of_mineral === 0) {
            if (is_null($image->mineral_id)) {
                return response()->json(['success' => 0, 'msgs' => ['error' => ['Это изображение не прикреплено к какому-либо минералу! Как оно может быть главным?'], 'success' => [], 'warning' => [], 'info' => []]], 406);
            }
            MineralsImage::where('mineral_id', $image->mineral_id)->where('main_image_of_mineral', true)->update(['main_image_of_mineral' => false]);

            try {
                $image->main_image_of_mineral = true;
                $image->save();
            } catch (\Exception $e) {
                MineralsImage::where('mineral_id', $image->mineral_id)->take(1)->update(['main_image_of_mineral' => true]);
                return response()->json(['success' => 0, 'msgs' => ['error' => ['Ошибка сервера.'], 'success' => [], 'warning' => [], 'info' => []]], 500);
            }
        }

        return response()->json(['success' => 1, 'msgs' => ['error' => [], 'success' => ['Новое главное изображение установлено.'], 'warning' => [], 'info' => []]], 200);
    }
}
