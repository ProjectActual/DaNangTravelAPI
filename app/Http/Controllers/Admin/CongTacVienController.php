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
     * show all user with role is CTV
     *
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $congTacVien = $this->congTacVienRepository
            ->with(['roles', 'posts'])
            ->sortByCTV()
            ->searchWithActive($request->status)
            ->paginate($this->paginate);
        return $this->responses(trans('notication.load.success'), 200, compact('congTacVien'));
    }

    /**
     * get information of CTV
     *
     * @param int $id this is the key to find a CTV
     * @return Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $congTacVien = $this->congTacVienRepository->with(['roles', 'posts'])->find($id);
        return $this->responses(trans('notication.load.success'), 200, compact('congTacVien'));
    }

    /**
     * approve the CTV registed and block account
     *
     * @param  AdminApprovedCTVRequest $request These are binding rules when requests are accepted
     * @param  int $id this is the key to find CTV and approve it
     * @return Illuminate\Http\Response
     */
    public function update(ApprovedCTVRequest $request, $id)
    {
        //get status old of CTV
        $statusOld = $this->checkApproveCTV($request->active, $id);
        $credentials = $request->only('active');
        if($request->active == User::ACTIVE[2]) {
            $credentials['birthday'] = null;
            $credentials['gender']   = null;
            $credentials['avatar']   = null;
            $credentials['phone']    = null;
        }
        $congTacVien = $this->congTacVienRepository->update($credentials, $id);
        $info = [
            'reason'    => empty($request->reason) ? '' : $request->reason,
        ];
        //Check if the submission request is in any format, and submit in that format
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
     * These are binding rules when requests are accepted
     *
     * @param  string $status status of CTV
     * @param  int $id this is the key to find the CTV
     * @return string status old of CTV
     */
    public function checkApproveCTV($status, $id)
    {
        $congTacVien = $this->congTacVienRepository->skipPresenter()->find($id);
        // Check if the collaborator has validated the email, if OK then approve
        if($congTacVien->active == User::ACTIVE[1]) {
            return $this->responseErrors('congTacVien', trans('validation_custom.credential.approved_email'));
        }
        // Check if the collaborator has validated the email, if ok, only select active status
        if($congTacVien->active == User::ACTIVE[2] && ($status == User::ACTIVE[1] || $status == User::ACTIVE[4])) {
            return $this->responseErrors('congTacVien', trans('validation_custom.credential.approved_admin'));
        }
        // Check if collaborators are active,if ok only select locked status
        if($congTacVien->active == User::ACTIVE[3] && $status != User::ACTIVE[4]) {
            return $this->responseErrors('congTacVien', trans('validation_custom.credential.approved'));
        }
        // Check if collaborators locked,if ok only select active status
        if($congTacVien->active == User::ACTIVE[4] && $status != User::ACTIVE[3] ) {
            return $this->responseErrors('congTacVien', trans('validation_custom.credential.approved'));
        }
        return $congTacVien->active;
    }

    /**
     * delete CTV
     * @param int $id this is the key to find CTV and approve it
     * @return Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $congTacVien = $this->congTacVienRepository->delete($id);
        return $this->responses(trans('notication.delete.success'), Response::HTTP_OK);
    }
}
