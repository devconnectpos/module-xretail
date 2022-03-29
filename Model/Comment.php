<?php
/**
 * Created by KhoiLe - mr.vjcspy@gmail.com
 * Date: 28/03/2018
 * Time: 10:37
 */

namespace SM\XRetail\Model;

use Magento\Config\Model\Config\CommentInterface;
use SM\XRetail\Helper\Data;

class Comment implements CommentInterface
{
    private $helper;

    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    public function getCommentText($elementValue)
    {
        $text = $this->helper->getCurrentVersion();
        $notice = "We are developing a new version of ConnectPOS API connector modules. The legacy modules will soon be deprecated and we will notify you about the changes. Stay tuned!";

        return "API Version: {$text}<br/><i>{$notice}</i>";
    }
}
