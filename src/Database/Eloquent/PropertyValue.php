<?php namespace Jiro\Product\Database\Eloquent;

use Jiro\Product\ProductInterface;
use Jiro\Product\PropertyValueInterface;
use Jiro\Property\Database\Eloquent\PropertyValue as BasePropertyValue;

/**
 *  Model for property values.
 *
 *  This abstract class must implement the appropriate methods. See docs.
 *
 * @author Andrew McLagan <andrewmclagan@gmail.com>
 */

class PropertyValue extends BasePropertyValue implements PropertyValueInterface
{
    /**
     * {@inheritdoc}
     */
    public function product()
    {
        return $this->hasOne('Jiro\Product\Database\Eloquent\Product','subject_id');
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct(ProductInterface $product = null)
    {
        $this->product()->associate($product);

        return $this;
    }
}
