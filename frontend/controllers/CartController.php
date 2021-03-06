<?php

namespace frontend\controllers;

use common\components\BaseController;
use common\models\Countries;
use common\models\OrderItems;
use common\models\Promocodes;
use frontend\models\Goods;
use frontend\models\Order;
use Yii;
use Worldpay\Worldpay;
use Worldpay\WorldpayException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;


/**
 * Site controller
 */
class CartController extends BaseController
{

    private $_user;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function getUser()
    {
        if ($this->_user == null) {
            $this->_user = Yii::$app->user->identity;
        }
        return $this->_user;
    }



    public function actionIndex()
    {
        $items = \Yii::$app->cart->items;
        $models = false;
        if ($items) {
            $models = Goods::find()->andWhere(['id' => array_keys($items)])->indexBy('id')->all();
        }

        if(Yii::$app->request->isPost){
            $paid = false;
        }

        $order = new Order();
        $order->setAttributes([
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'email' => $this->user->email,
            'address' => $this->user->shipping_address,
            'address_optional' => $this->user->shipping_address_optional,
            'country_id' => Yii::$app->cart->country_id,
            'city' => $this->user->shipping_city,
            'postcode' => $this->user->shipping_postcode,
            'phone' => $this->user->shipping_phone,
//            'promocode' =>  ,
//            'promocode' => 213 ,
        ], false);

        $order->promocode = Yii::$app->cart->promocode ?  Yii::$app->cart->promocode->code : '';
        if ($order->load(\Yii::$app->request->post()) && $items && $models  && $order->save()){

            Yii::$app->cart->updateCountry($order->country_id);
            $sum = 0;
//            $sumPrice = 0;
            $tax = 0;
            $discountSum = 0;
            $shipping = Yii::$app->cart->shipping;
            $mailItems = [];
                foreach ($items  as $key => $count) {
                    if (isset($models[$key])) {
                        $mailItems[] = [
                            'title' => $models[$key]['title'],
                            'price' => $models[$key]['regular_price']
                        ];
                        $sum += $models[$key]->total * $count;
                        $tax += $models[$key]->tax * $count;
                        $shipping += $models[$key]->extra_shipping* $count;

                        $orderItem = new OrderItems();
                        $orderItem->link('goods',$models[$key]);
                        $orderItem->link('order',$order);
                        $orderItem->count = $count ;
                        $orderItem->price = $models[$key]->price;
                        $orderItem->price_tax = $models[$key]->tax;

                        if ($models[$key]->virtualItem) {
                            $orderItem->holding_charge = $models[$key]->virtualItem['price'];
                            $orderItem->holding_charge_tax = $models[$key]->virtualItem['tax'];

                            $sum += $orderItem->holding_charge * $count;
                            $tax += $orderItem->holding_charge_tax * $count;
                        }
                        $orderItem->save(false);


                    }


                }


            $discountSum = Yii::$app->cart->promocode ?  Yii::$app->cart->promoTotal : '';

//                if ($order->promocodes_id){
//                    $promocode = Promocodes::find()
////            ->andWhere(['>=','start_date',date('Y-m-d')])->orWhere(['start_date'=>null])
//                        ->andWhere("start_date<=".date('Y-m-d').' or start_date is null')
//                        ->andWhere(['id'=>$order->promocodes_id,'status'=>Promocodes::STATUS_ACTIVE])
//                        ->andWhere(['>=','end_date',date('Y-m-d')])->one();
//
//                        if ($promocode){
//                            if ($promocode->sum){
//                                $discountSum = $sum - $promocode->sum;
////                                $discountTax = $tax - $promocode->sum;
//                                $discountSum = $sum >0 ? $sum : 0;
//                            } elseif ($promocode->percent){
//                                $discountSum = $sum - ($sum*$promocode->percent/100);
//                            }
//                        }
//                }

                $order->updateAttributes([
                    'total_sum'=>$sum,
                    'total_tax' => $tax,
                    'tax_percent' => Yii::$app->cart->tax,
//                    'total_tax_discount' => $discountTax,
                    'total_sum_discount'=>$discountSum,
                    'shipping_cost' => $shipping,
                ]);

            $request = Yii::$app->request;
            $token = $request->post('token');
            $worldpay = new Worldpay(Yii::$app->params['worldpay_api_service_key']);


            $billing_address = array(
                "address1"=>$order->address,
                "address2"=> $order->address_optional,
                "address3"=> '',
                "postalCode"=> $order->postcode,
                "city"=> $order->city,
                "state"=> 'ffsdfsd',
                "countryCode"=> 'GB',
            );


            try {
                $response = $worldpay->createOrder(array(
                    'token' => $token,
                    'amount' => $order->total_sum*100 + $order->shipping_cost*100,
                    'currencyCode' => 'GBP',
                    'name' => $this->user->first_name . ' ' . $this->user->last_name,
                    'billingAddress' => $billing_address,
                    'orderDescription' => $order->note ? $order->note : 'Description',
                    'customerOrderCode' => $order->id
                ));
                if ($response['paymentStatus'] === 'SUCCESS') {
                    $worldpayOrderCode = $response['orderCode'];
                    $order->worldpay_order_id = $worldpayOrderCode;
                    $order->worldpay_order_status = $response['paymentStatus'];
                    $paid = true;
                    Yii::$app->cart->destroyCart();
                } else {
                    //throw new WorldpayException(print_r($response, true));
                }
            } catch (WorldpayException $e) {
                /*echo 'Error code: ' .$e->getCustomCode() .'
                    HTTP status code:' . $e->getHttpStatusCode() . '
                    Error description: ' . $e->getDescription()  . '
                    Error message: ' . $e->getMessage();*/
                $order->worldpay_order_status = $e->getMessage();

        } catch (Exception $e) {
                echo 'Error message: '. $e->getMessage();
            }

            $order->save();

            /*
            $body = Yii::$app->view->renderFile ( '@common/mail/orderDetails-html.php' , [
                'total_sum' => $order->total_sum,
                    'total_tax' => $order->total_tax,
                    'tax_percent' => $order->tax_percent,
                    'shipping_cost' => $order->shipping_cost,
                    'holding_deposit' =>  Yii::$app->cart->subHoldingDeposit ? Yii::$app->cart->subHoldingDeposit : 0,
                    'items' => $mailItems
            ] );
            */

            Yii::$app
                ->mailer
                ->compose('orderDetails-html',[
                    'total_sum' => $order->total_sum,
                    'total_tax' => $order->total_tax,
                    'tax_percent' => $order->tax_percent,
                    'shipping_cost' => $order->shipping_cost,
                    'holding_deposit' =>  Yii::$app->cart->subHoldingDeposit ? Yii::$app->cart->subHoldingDeposit : 0,
                    'items' => $mailItems
                ])
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
                ->setTo($order->email)
                ->setSubject('New order at ' . Yii::$app->name)
                ->send();

            return $this->redirect($order->url);
        }

        return $this->render('cart', ['items' => $items, 'models' => $models,'order'=>$order]);

    }

    public function actionAddItem($id, $qty = 1)
    {
        \Yii::$app->response->format = 'json';

        $model = Goods::findOne(['id' => $id]);

        if (!$model) {
            return self::returnError(self::ERROR_NOTFOUND, "Can't add item to cart");
        }

        \Yii::$app->cart->addItem($id, $qty);

        return

            [
                'item' => [
                    'id' => $id,
                    'html' => $this->renderPartial('_cart_view', ['model' => $model, 'count' => \Yii::$app->cart->getItemCount($id)])
                ],

                'cart' => \Yii::$app->cart->cartInfo()
            ];
    }

    public function actionChangeQuantity($id, $qty = 1)
    {
        \Yii::$app->response->format = 'json';

        $model = Goods::findOne(['id' => $id]);

        if (!$model) {
            return self::returnError(self::ERROR_NOTFOUND, "Can't add item to cart");
        }

        \Yii::$app->cart->setQuantity($id, $qty);

        return

            [
                'item' => [
                    'id' => $id,
                    'html' => $this->renderPartial('_cart_view', ['model' => $model, 'count' => \Yii::$app->cart->getItemCount($id)])
                ],

                'cart' => \Yii::$app->cart->cartInfo()
            ];
    }

    public function actionSetCountry($id){
        $country = Countries::findOne(['id'=>$id]);

        Yii::$app->response->format = 'json';
        if ($country){
            $session = \Yii::$app->session;
            $c  =  $session->set('country', $country->id);
            Yii::$app->cart->country_id = $country->id;
            Yii::$app->cart->country = $country;

        } else {
            return self::returnError(self::ERROR_NOTFOUND);
        }

        return

            [


                'cart' => \Yii::$app->cart->cartInfo()
            ];

    }

    public function actionPromocodeApply($promocode)
    {
        \Yii::$app->response->format = 'json';
        $model = Promocodes::findPromocode($promocode);
        if (!$model) {
            return self::returnError(self::ERROR_NOTFOUND, 'Promocode expired or not found.');
        }


        $session = \Yii::$app->session;
          $session->set('promocode', $promocode);


        return
            [
                'item' => null,
                'cart' => \Yii::$app->cart->cartInfo()
            ];




    }

    public function actionDeleteItem($id)
    {
        if (\Yii::$app->request->isAjax) {

            \Yii::$app->response->format = 'json';


            $model = Goods::findOne(['id' => $id]);

            if (!$model) {
                return self::returnError(self::ERROR_NOTFOUND, "Can't delete item from cart. Item not found");
            }

            \Yii::$app->cart->deleteItem($id);


            $return =

                [
                    'item' => [
                        'id' => $id,
                        'html' => "Removed item $id from cart"
                    ],

                    'cart' => \Yii::$app->cart->cartInfo()
                ];

            return $return;
        }
    }

    public function actionDeleteItemFull($id)
    {
        if (\Yii::$app->request->isAjax) {

            \Yii::$app->response->format = 'json';


            $model = Goods::findOne(['id' => $id]);

            if (!$model) {
                return self::returnError(self::ERROR_NOTFOUND, "Can't delete item from cart. Item not found");
            }

            \Yii::$app->cart->deleteItemFull($id);


            $return =

                [
                    'item' => [
                        'id' => $id,
                        'html' => "Removed item $id from cart"
                    ],

                    'cart' => \Yii::$app->cart->cartInfo()
                ];

            return $return;
        }
    }

    public function actionDeleteCart()
    {
    }

    public function actionGetCart()
    {
        $items = \Yii::$app->cart->items;
        $response_data = [];
        \Yii::$app->response->format = 'json';
        $products = [];
        $total_sum = 0;
        $count = 0;

        if(!empty($items))
        {
            foreach ($items as $id=>$quantity){
                $good = Goods::findOne($id);
                if(!empty($good)){
                    $count += $quantity;
                    $product = new \stdClass();
                    $product->id = $id;
                    $product->title = $good->title;
                    $product->quantity = $quantity;
                    $product->price = \Yii::$app->formatter->asCurrency($good->regular_price);
                    $products[] = $product;
                    $total_sum += $quantity * $good->regular_price;
                }
            }
            $response_data['code'] = 200;
            $response_data['products']= $products;
            $response_data['count']= $count;
            $response_data['total_sum'] = \Yii::$app->formatter->asCurrency($total_sum);
            return $response_data;
        }
        $response_data['code'] = 440;
    }



}