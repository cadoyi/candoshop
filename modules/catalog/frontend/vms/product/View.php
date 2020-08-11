<?php

namespace catalog\frontend\vms\product;

use Yii;
use cando\web\ViewModel;
use catalog\models\Category;

/**
 * product view
 *
 * @author  zhangyang <zhangyangcado@qq.co>
 */
class View extends ViewModel
{


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->addBreadcrumbs();
    }




    /**
     * 设置导航条
     */
    public function addBreadcrumbs()
    {
        $category = $this->product->category;
        $path = $category->path;
        $ids = explode('/', $path);
        $count = count($ids);
        switch($count) {
            case 1:
               break;
            case 2:
               $parent = $category->parent;
               $this->getView()->addBreadcrumb($parent->title, ['view', 'id' => $parent->id]);
               break;
            default:
                array_pop($ids);
                $categories = Category::find()
                   ->select(['title'])
                   ->indexBy('id')
                   ->orderBy(['path' => SORT_ASC])
                   ->andWhere(['in', 'id', $ids])
                   ->tableCache()
                   ->column();
                foreach($categories as $id => $title) {
                    $this->getView()->addBreadcrumb($title, ['view', 'id' => $id]);
                }
            break;
        }
        $this->getView()->addBreadcrumb($category->title, ['/catalog/category/view', 'id' => $category->id]);

    }

}