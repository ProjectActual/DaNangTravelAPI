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
    protected $cong_tac_vien;
    protected $paginate = 15;

    public function __construct(UserRepositoryEloquent $cong_tac_vien)
    {
        $this->cong_tac_vien = $cong_tac_vien;

        $this->cong_tac_vien->pushCriteria(new FilterByCongTacVienCriteria());
    }

    public function index(Request $request)
    {
        $cong_tac_vien = $this->cong_tac_vien->sortByCTV()->paginate($this->paginate);

        return $this->responses(trans('notication.load.success'), 200, compact('cong_tac_vien'));
    }

    public function show(Request $request, $id)
    {
        $cong_tac_vien = $this->cong_tac_vien->find($id);

        return $this->responses(trans('notication.load.success'), 200, compact('cong_tac_vien'));
    }

/**
 * Phê duyệt cộng tác viên đăng ký
 * @param  AdminApprovedCTVRequest $request [giá trị từ client gửi lên]
 * @param  [type]                  $id      [id người dùng cần phê duyệt]
 * @return [type]                           [API json]
 */
    public function update(ApprovedCTVRequest $request, $id)
    {
        $this->cong_tac_vien->skipPresenter();
        $cong_tac_vien = $this->cong_tac_vien->find($id);

        // Kiểm tra nếu cộng tác viên đã được duyệt thì sẽ không cho duyệt nửa
        if($cong_tac_vien->admin_active == User::ADMIN_ACTIVE[1]) {
            return $this->responseErrors('cong_tac_vien', trans('validation_custom.credential.approved'));
        }

        if($cong_tac_vien->active == User::ACTIVE[2]) {
            return $this->responseErrors('cong_tac_vien', trans('validation_custom.credential.approved_email'));
        }

        $cong_tac_vien->admin_active = $request->admin_active;

        $cong_tac_vien->save();

        $info = [
            'reason'    => empty($request->reason) ? '' : $request->reason,
        ];

        //Kiểm tra nếu yêu cầu gửi lên là không duyệt thì send mail và xóa dữ liệu cũ
        if($cong_tac_vien->admin_active == User::ADMIN_ACTIVE[1]) {
            $info['message'] = trans('notication.email.admin.success');

            SendMail::send(
                $cong_tac_vien->email,
                trans('notication.email.admin.success'),
                'email.admin_credentials.success',
                $info
            );
        } else {
            $info['message'] = trans('notication.email.admin.fail');

            SendMail::send(
                $cong_tac_vien->email,
                trans('notication.email.admin.fail'),
                'email.admin_credentials.fail',
                $info
            );
        }

        return $this->responses(trans('notication.email.admin.approved'), 200);
    }

    public function destroy(Request $request, $id)
    {
        $this->cong_tac_vien->skipPresenter();
        $cong_tac_vien = $this->cong_tac_vien->find($id);

        if (empty($cong_tac_vien)) {
            return $this->responseException('Incorect route', Response::HTTP_NOT_FOUND);
        }

        $cong_tac_vien->delete();

        return $this->responses(trans('notication.delete.success'), Response::HTTP_OK);
    }

    public function block(BlockRequest $request, $id)
    {
        $this->cong_tac_vien->skipPresenter();
        $cong_tac_vien = $this->cong_tac_vien->find($id);

        if($cong_tac_vien->admin_active == User::ADMIN_ACTIVE[2]) {
            return $this->responseErrors('cong_tac_vien', trans('validation_custom.credential.approved_admin'));
        }

        if($cong_tac_vien->active == User::ACTIVE[2]) {
            return $this->responseErrors('cong_tac_vien', trans('validation_custom.credential.approved_email'));
        }

        if($cong_tac_vien->is_block == $request->is_block) {
            return $this->responseErrors('cong_tac_vien', trans('validation_custom.change'));
        }

        $cong_tac_vien->is_block = $request->is_block;

        $cong_tac_vien->save();

        $info = [
            'reason'    => empty($request->reason) ? '' : $request->reason,
        ];

        if($cong_tac_vien->is_block == User::IS_BLOCK[2]) {
            $info['title'] = trans('notication.email.block.unlock.title');
            $info['message'] = trans('notication.email.block.unlock.message');
        } else {
            $info['title'] = trans('notication.email.block.lock.title');
            $info['message'] = trans('notication.email.block.lock.message');
        }

        SendMail::send(
            $cong_tac_vien->email,
            trans('notication.email.block.info'),
            'email.block',
            $info
        );

        return $this->responses(trans('notication.email.block.success'), 200);
    }
}
