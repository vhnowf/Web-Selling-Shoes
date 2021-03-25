<?php
require_once 'controllers/Controller.php';
require_once 'models/Product.php';
require_once 'models/Category.php';
require_once 'models/Pagination.php';
class ProductController extends Controller {

  public function detail()
  {
      $name = str_replace('-', ' ', $_GET['name']);
      $product_model = new Product();
      $product = $product_model->getProductByName($name);
      $size = $product_model->getSize($name);
      $suggestProducts = $product_model->getSuggestProducts($name);
      $this->content = $this->render('views/products/detail.php', [
          'product' => $product,
          'suggestProducts' => $suggestProducts,
          'size' => $size
      ]);
      $product_id = $product['id'];
      if(isset($_POST['addtocart'])){
          $cart_item = [
              'name' => $product['title'],
              'current_price' => $product['current_price'],
              'avatar' => $product['avatar'],
              'quantity' => 1
              
          ];
          if (!isset($_SESSION['cart'])||!isset($_SESSION['totalPrice'])||!isset($_SESSION['totalItemPrice'])||!isset($_SESSION['totalQuantity'])) {
              $_SESSION['cart'][$product_id] = $cart_item;
              $_SESSION['totalItemPrice'][$product_id] = 0;
              $_SESSION['totalQuantity'] = 0;
          } else {
              if (array_key_exists($product_id, $_SESSION['cart'])) {
                  $_SESSION['cart'][$product_id]['quantity']++;

              }
              else {
                  $_SESSION['cart'][$product_id] = $cart_item;

              }
          }
          $_SESSION['totalQuantity']++;
          $_SESSION['totalItemPrice'][$product_id] = $_SESSION['cart'][$product_id]['quantity'] * $_SESSION['cart'][$product_id]['current_price'];
          $_SESSION['totalPrice'] += $_SESSION['cart'][$product_id]['current_price'];
          header('Location:gio-hang-cua-ban.html');
//          exit();
      }
      require_once 'views/layouts/main.php';
  }
//    public function add_detail(){
//        $name = str_replace('-', ' ', $_GET['name']);
//        $product_model = new Product();
//        $product = $product_model->getProductByName($name);
//        $product_id = $product['id'];
//
//
//
//  }

  public function showAllProductsByCategory() {
    $name = $_GET['name'];
    $product_model = new Product();
    $count_total = $product_model->countTotal();
    $query_additional = '';
    if (isset($_GET['name'])) {
      $query_additional .= '&name=' . $_GET['name'];
    }
    if (isset($_GET['orderBy'])) {
      $query_additional .= '&orderBy=' . $_GET['orderBy'];
    }
    if (isset($_GET['filterPrice']) && isset($_GET['price'])) {
      // if (isset($_GET['price'])) {
      //   $query_additional .= '&filterPrice=Filter' . '&price=' . $_GET['price'];
      // }
      $query_additional .= '&filterPrice=Filter' . '&price=' . $_GET['price'];

      // if (isset($_GET['price2'])) {
      //   $query_additional .= '&filterPrice=' . $_GET['price2'];
      // }
      // if (isset($_GET['price3'])) {
      //   $query_additional .= '&filterPrice=' . $_GET['price3'];
      // }
      // if (isset($_GET['price4'])) {
      //   $query_additional .= '&filterPrice=' . $_GET['price4'];
      // }
    }
    $arr_params = [
        'total' => $count_total,
        'limit' => 12,
        'query_string' => 'page',
        'controller' => 'product',
        'action' => 'showAllProductsByCategory',
        'full_mode' => false,
        'query_additional' => $query_additional,
        'page' => isset($_GET['page']) ? $_GET['page'] : 1
    ];
    $products = $product_model->getAllPagination($arr_params);
    $pagination = new Pagination($arr_params);
    $pages = $pagination->getPagination();
    
    $this->content = $this->render('views/products/show_all.php',[
      'products' => $products,
      'pages' => $pages,
    ]);
    require_once 'views/layouts/main.php';

  }

  public function saleProducts() {
    $product_model = new Product();
    $count_total = $product_model->countTotalProductsSale();
    $arr_params = [
      'total' => $count_total,
      'limit' => 12,
      'query_string' => 'page',
      'controller' => 'product',
      'action' => 'saleProducts',
      'full_mode' => false,
      'page' => isset($_GET['page']) ? $_GET['page'] : 1
    ];
    $products = $product_model->getProductsSale($arr_params);
    $pagination = new Pagination($arr_params);
    $pages = $pagination->getPagination();
    $this->content = $this->render('views/products/show_all.php',[
      'products' => $products,
      'pages' => $pages
    ]);
    require_once 'views/layouts/main.php';
  }
    public function searchProducts(){
        $product_model = new Product();
        $name = $_POST['search'];
        $products = $product_model->searchProducts($name);
        $this->content = $this->render('views/products/search.php',[
            'products' => $products
        ]);
        require_once 'views/layouts/main.php';
    }

    
}