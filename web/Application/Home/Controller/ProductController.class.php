<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2016/5/15
 * Time: 14:32
 */

namespace Home\Controller;
use Home\Model\CategoryModel;
use Think\Controller;

class ProductController extends Controller{

    public function category(){
        $result = M("Category")->select();
        $this->assign("list",$result);
        $this->display();
    }

    public function category_add(){
        if(IS_GET){
            $this->display('category_edit');
        }else if(IS_POST){
            $category = D('Category');
            //$category = new CategoryModel();
            if($category->create()){
                $result = $category->add();
                if($result){
                    $this->redirect("category","新增成功");
                }else{
                    $this->error("数据添加错误",U("category"));
                }
            }else{
                $this->error($category->getError());
            }
        }
    }

    public function category_del($ids){
        //type1
        //$idArr = explode(",",$ids);
        //$category = M("Category");
        //foreach($idArr as $id){
        //    $category->delete($id);
        //}
        //type2
        M("Category")->delete($ids);
        $this->redirect("category");
    }

    public function category_mod($id){
        if(IS_GET){
            $obj = M("Category")->find($id);
            if($obj){
                $this->assign("category",$obj);
                $this->display('category_edit');
            }else{
                $this->error("找不到对象",U("category"),3);
            }
        }else if(IS_POST){
            $category = D("Category");
            if($category->create()){//mod should be update
                $result = $category->save();
                if($result){
                    $this->redirect("category","保存成功");
                }else{
                    $this->error("数据修改错误",U("category"));
                }
            }else{
                $this->error($category->getError());
            }
        }
        
        
    }

    /*
     * 产品
     */
    public function index($name=NULL,$category_id=Null,$pageSize=10){
        //根据名称、类别分页查询
        if($name){
            $condition['name']=array('like','%'.I('get.name').'%');
        }
        if($category_id){
            $condition['category_id']=I('get.category_id');
        }
        
        $Product = D("ProductView");
        $page=getpage($Product,$condition);
        //$count = $Product->where($condition)->count();
        //$page = new \Think\Page($count,$pageSize);
        // 进行分页数据查询
        $result = $Product->where($condition)->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('list',$result);
        //setPageStyle($page);
        $this->assign('page',$page->show());
        var_dump($page);
        //增加其他信息
        $query['name']=$name;
        $query['category_id']=$category_id;
        $this->assign('query',$query);
        $this->addCategory();
        $this->display();
    }

    public function add(){
        if(IS_GET){
            $this->addCategory();
            $this->display('edit');
        }else if(IS_POST){
            $product = D("Product");
            if($product->create()){
                $result = $product->add();
                if($result){
                    $this->redirect("index","新增成功");
                }else{
                    $this->error("数据添加错误",U("index"));
                }
            }else{
                $this->error($product->getError());
            }
        }
        
    }

    public function update($id){
        if(IS_GET){
            $obj = M("Product")->find($id);
            if($obj){
                $this->addCategory();
                $this->assign("product",$obj);
                $this->display('edit');
            }else{
                $this->error("找不到对象",U("index"),3);
            }
        }else if(IS_POST){
            $product = D("Product");
            if($product->create()){
                $product->save();
                $this->redirect("index","修改成功");
            }else{
                $this->error($product->getError());
            }
        }
        
    }

    public function del($ids){
        M("Product")->delete($ids);
        $this->redirect("index");
    }

    private function addCategory(){
        $this->assign('categorys',M('Category')->select());
    }
}