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
    protected $paginate = 3;

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
     * Phê duyệt cộng tác viên đăng ký, block tài khoản
     * @param  AdminApprovedCTVRequest $request đây là những nguyên tắc ràng buộc khi request được chấp nhận
     * @param  int                  $id      id người dùng cần phê duyệt
     * @return object
     */
    public function update(ApprovedCTVRequest $request, $id)
    {
        $statusOld = $this->checkApproveCTV($request->active, $id);

        $congTacVien = $this->congTacVienRepository->update($request->all(), $id);

        $info = [
            'reason'    => empty($request->reason) ? '' : $request->reason,
        ];

        //Kiểm tra nếu yêu cầu gửi lên là thuộc dạng status nào và sent mail
        if($congTacVien->active == User::ACTIVE[3] && $statusOld == User::ACTIVE[2]) {
            $info['title'] = trans('notication.email.admin.success.title');
            $info['message'] = trans('notication.email.admin.success.message');
        } elseif ($congTacVien->active == User::ACTIVE[2]) {
            $info['title'] = trans('notication.email.admin.fail.title');
            $info['message'] = trans('notication.email.admin.fail.message');
        } elseif ($congTacVien->active == User::ACTIVE[3] && $statusOld == User::ACTIVE[4]) {
            $info['title'] = trans('notication.email.block.unlock.title');
            $info['message'] = trans('notication.email.block.unlock.message');
        }else {
            $info['title'] = trans('notication.email.block.lock.title');
            $info['message'] = trans('notication.email.block.lock.message');
        }

        SendMail::send(
            $congTacVien->email,
            trans('notication.email.credential.status'),
            'email.admin_credential',
            $info
        );

        return $this->responses(trans('notication.edit.success'), 200);
    }

    /**
     * Kiểm tra xem dữ liệu đưa vào có đúng yêu cầu đưa ra
     * @param  string $status tình trạng của cộng tác viên
     * @param  int $id     khóa để tìm kiếm cộng tác viên
     * @return string tình trạng cũ của cộng tác viên
     */
    public function checkApproveCTV($status, $id)
    {
        $congTacVien = $this->congTacVienRepository->skipPresenter()->find($id);

        // Kiểm tra nếu cộng tác viên đã xác thực mail chưa, nếu rồi mới cho duyệt
        if($congTacVien->active == User::ACTIVE[1]) {
            return $this->responseErrors('congTacVien', trans('validation_custom.credential.approved_email'));
        }

        // Kiểm tra nếu cộng tác viên đã xác thực mail thì sẽ chỉ cho chọn active
        if($congTacVien->active == User::ACTIVE[2] && ($status == User::ACTIVE[1] || $status == User::ACTIVE[4])) {
            return $this->responseErrors('congTacVien', trans('validation_custom.credential.approved_admin'));
        }

        // Kiểm tra nếu cộng tác viên đã active thì sẽ chỉ cho chọn locked
        if($congTacVien->active == User::ACTIVE[3] && $status != User::ACTIVE[4]) {
            return $this->responseErrors('congTacVien', trans('validation_custom.credential.approved'));
        }

        // Kiểm tra nếu cộng tác viên đã locked thì sẽ chỉ cho chọn active
        if($congTacVien->active == User::ACTIVE[4] && $status != User::ACTIVE[3] ) {
            return $this->responseErrors('congTacVien', trans('validation_custom.credential.approved'));
        }
        return $congTacVien->active;
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
}
