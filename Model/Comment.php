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
        $this->helper          = $helper;
    }

    public function getCommentText($elementValue)
    {
        $text = $this->helper->getCurrentVersion();
        return "API Version: ".$text;
    }
}
