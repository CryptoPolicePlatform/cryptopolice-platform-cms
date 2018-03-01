<?php namespace CryptoPolice\Platform\Components;

use DB;
use Auth;
use Flash;
use Session;
use Validator;
use Cms\Classes\ComponentBase;
use CryptoPolice\Platform\Classes\Helpers;
use CryptoPolice\Platform\Models\CommunityPost;
use CryptoPolice\Platform\Models\CommunityComment;
use CryptoPolice\Academy\Components\Recaptcha as Recaptcha;


class PostComments extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Posts list',
            'description' => 'Community Posts List'
        ];
    }


    public function onRun()
    {
        $comments = CommunityComment::withTrashed()->with('user.avatar','user.groups')
            ->where('post_id', $this->param('id'))
            ->orderBy('created_at', 'desc')
            ->get();

        $this->page['comments'] = $this->makeArrayTree($comments);
    }


    private function build_sorter()
    {
        return function ($firstCommentReply, $secondSecondReply)
        {
            return strnatcmp($firstCommentReply->created_at, $secondSecondReply->created_at);
        };
    }

    private function makeArrayTree($comments)
    {

        $childs = [];

        foreach ($comments as $comment) {
            $childs[$comment->parent_id][] = $comment;
        }

        foreach ($comments as $comment) {
            if (isset($childs[$comment->id])) {
                usort($childs[$comment->id], $this->build_sorter());
                $comment->childs = $childs[$comment->id];
            }
        }

        count($childs) > 0 ? $tree = $childs[0] : $tree = [];
        return $tree;
    }


    public function onAddComment()
    {

        Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            $helper = new Helpers();

            if ($helper->checkLinks(input('description'))) {
                Flash::error('Links are not allowed');
            } else {

                $user = Auth::getUser();

                $comment = new CommunityComment;
                $comment->user_id = $user->id;
                $comment->post_id = $this->param('id');
                $comment->description = input('description');
                if (!empty(input('parent_id'))) {
                    $comment->parent_id = input('parent_id');
                }
                $comment->save();

                Flash::success('Your comment has been successfully added');
                return redirect()->back();
            }
        }
    }

    public function onDeleteComment()
    {

        $user = Auth::getUser();

        if ($user && !empty(post('id'))) {
            CommunityComment::where('user_id', $user->id)->where('id', post('id'))->delete();
            Flash::warning('Your comment has been successfully deleted');
            return redirect()->back();
        }
    }
}