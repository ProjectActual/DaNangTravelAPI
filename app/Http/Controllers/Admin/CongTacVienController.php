<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\BaseController;

use App\Entities\User;
use App\Services\SendMail;
use App\Http\Requests\Admin\BlockRequest;
use App\Criteria\FilterByCongTacVienCriteria;
use App\Http\Requests\Admin\ApprovedCTVRequest;
use App\Repositories\Eloquents\UserRepositoryEloquent;

class CongTacVienController extends BaseController
{
    /**
     * @var int
     */
    protected $paginate = 15;

    /**
     * @var repository
     */
    protected $congTacVienRepository;


    public function __construct(UserRepositoryEloquent $congTacVienRepository)
    {
        $this->congTacVienRepository = $congTacVienRepository;

        $this->congTacVienRepository->pushCriteria(new FilterByCongTacVienCriteria());
    }

    /**
     * Hiển thị tất cả CTV
     * @param  Request $request
     * @return object
     */
    public function index(Request $request)
    {
        $congTacVien = $this->congTacVienRepository->sortByCTV()->paginate($this->paginate);

        return $this->responses(trans('notication.load.success'), 200, compact('congTacVien'));
    }

    /**
     * thông tin chi tiết của CTV
     * @param int $id đây là id tìm kiếm của CTV
     * @return object
     */
    public function show(Request $request, $id)
    {
        $congTacVien = $this->congTacVienRepository->find($id);

        return $this->responses(trans('notication.load.success'), 200, compact('congTacVien'));
    }

    /**
     * Phê duyệt cộng tác viên đăng ký
     * @param  AdminApprovedCTVRequest $request đây là những nguyên tắc ràng buộc khi request được chấp nhận
     * @param  int                  $id      id người dùng cần phê duyệt
     * @return object
     */
    public function update(ApprovedCTVRequest $request, $id)
    {
        $this->congTacVienRepository->skipPresenter();
        $congTacVien = $this->congTacVienRepository->find($id);

        // Kiểm tra nếu cộng tác viên đã được duyệt thì sẽ không cho duyệt nửa
        if($congTacVien->admin_active == User::ADMIN_ACTIVE[1]) {
            return $this->responseErrors('congTacVien', trans('validation_custom.credential.approved'));
        }

        if($congTacVien->active == User::ACTIVE[2]) {
            return $this->responseErrors('congTacVien', trans('validation_custom.credential.approved_email'));
        }

        $congTacVien->admin_active = $request->admin_active;

        $congTacVien->save();

        $info = [
            'reason'    => empty($request->reason) ? '' : $request->reason,
        ];

        //Kiểm tra nếu yêu cầu gửi lên là không duyệt thì send mail và xóa dữ liệu cũ
        if($congTacVien->admin_active == User::ADMIN_ACTIVE[1]) {
            $info['message'] = trans('notication.email.admin.success');

            SendMail::send(
                $congTacVien->email,
                trans('notication.email.admin.success'),
                'email.admin_credentials.success',
                $info
            );
        } else {
            $info['message'] = trans('notication.email.admin.fail');

            SendMail::send(
                $congTacVien->email,
                trans('notication.email.admin.fail'),
                'email.admin_credentials.fail',
                $info
            );
        }

        return $this->responses(trans('notication.email.admin.approved'), 200);
    }

    /**
     * xóa CTV theo id
     * @param int $id đây là id tìm kiếm của CTV
     * @return object
     */
    public function destroy(Request $request, $id)
    {
        $congTacVien = $this->congTacVienRepository->delete($id);

        return $this->responses(trans('notication.delete.success'), Response::HTTP_OK);
    }

    public function block(BlockRequest $request, $id)
    {
        $this->congTacVienRepository->skipPresenter();
        $congTacVien = $this->congTacVienRepository->find($id);

        if($congTacVien->admin_active == User::ADMIN_ACTIVE[2]) {
            return $this->responseErrors('congTacVien', trans('validation_custom.credential.approved_admin'));
        }

        if($congTacVien->active == User::ACTIVE[2]) {
            return $this->responseErrors('congTacVien', trans('validation_custom.credential.approved_email'));
        }

        if($congTacVien->is_block == $request->is_block) {
            return $this->responseErrors('congTacVien', trans('validation_custom.change'));
        }

        $congTacVien->is_block = $request->is_block;

        $congTacVien->save();

        $info = [
            'reason'    => empty($request->reason) ? '' : $request->reason,
        ];

        if($congTacVien->is_block == User::IS_BLOCK[2]) {
            $info['title'] = trans('notication.email.block.unlock.title');
            $info['message'] = trans('notication.email.block.unlock.message');
        } else {
            $info['title'] = trans('notication.email.block.lock.title');
            $info['message'] = trans('notication.email.block.lock.message');
        }

        SendMail::send(
            $congTacVien->email,
            trans('notication.email.block.info'),
            'email.block',
            $info
        );

        return $this->responses(trans('notication.email.block.success'), 200);
    }
}
