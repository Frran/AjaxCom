<?php

namespace DM\AjaxCom;

use DM\AjaxCom\Responder\Container\FlashMessage;
use DM\AjaxCom\Responder\Container\Container;
use DM\AjaxCom\Responder\ChangeUrl;
use DM\AjaxCom\Responder\Callback;
use DM\AjaxCom\Responder\Modal;
use DM\AjaxCom\Responder\ResponderInterface;

class Handler
{

    /**
     * Collection of ResponderInterface objects
     *
     * @var array $queue
     */
    private $queue = array();

    /**
     * Add ResponderInterface object to the queue
     *
     * @var ResponderInterface $responder
     * @return Handler
     */
    public function register(ResponderInterface $responder)
    {
        $this->queue[] = $responder;
        return $this;
    }

    /**
     * Remove ResponderInterface object from the queue
     *
     * @var ResponderInterface $responder
     * @return Handler
     */
    public function unregister(ResponderInterface $responder)
    {
        $key = array_search($responder, $this->queue, true);
        if ($key!==false) {
            unset($this->queue[$key]);
        }
        return $this;
    }

    /**
     * Create a FlashMessage and register it to the queue
     *
     * Convenience method to allow for a fluent interface
     *
     * @var string $message
     * @var string $type
     * @return FlashMessage
     */
    public function flashMessage($message, $type = FlashMessage::TYPE_SUCCESS)
    {
        $message = new FlashMessage($message, $type);
        $this->register($message);
        return $message;
    }

    /**
     * Create a Container and register it to the queue
     *
     * Convenience method to allow for a fluent interface
     *
     * @var string $identifier
     * @return Container
     */
    public function container($identifier)
    {
        $container = new Container($identifier);
        $this->register($container);
        return $container;
    }

    /**
     * Create a Modal and register it to the queue
     *
     * Convenience method to allow for a fluent interface
     *
     * @var string html
     * @var string type
     * @return Modal
     */
    public function modal($html, $type = Modal::DEFAULT_TYPE)
    {
        $modal = new Modal($html, $type);
        $this->register($modal);
        return $modal;
    }

    /**
     * Create a Callback and register it to the queue
     *
     * Convenience method to allow for a fluent interface
     *
     * @var string $function
     * @var mixed $params Parameters sent to the callback function
     * @return Callback
     */
    public function callback($function, $params = null)
    {
        $callback = new Callback($function, $params);
        $this->register($callback);
        return $callback;
    }

    /**
     * Create a ChangeUrl and register it to the queue
     *
     * Convenience method to allow for a fluent interface
     *
     * @var string $url
     * @var string $method push|replace|redirect
     * @var int $wait - wait before action
     * @return ChangeUrl
     */
    public function changeUrl($url, $method = ChangeUrl::PUSH, $wait = 0)
    {
        $changeUrl = new ChangeUrl($url, $method, $wait);
        $this->register($changeUrl);
        return $changeUrl;
    }

    /**
     * Generates response
     *
     * @return
     */

    public function respond()
    {
        $response = array();
 
        foreach ($this->queue as $object) {
            $response[] = $object->render();
        }
        
        return array('ajaxcom'=>$response);
    }
}
