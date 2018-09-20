<?php

namespace PE\Bundle\OAuth2ClientBundle\Model;

class Button
{
    private $type;
    private $href;
    private $text;
    private $icon;
    private $class;
    private $attr;

    /**
     * @param string $type
     * @param string $href
     * @param string $text
     * @param string $icon
     * @param string $class
     * @param array  $attr
     */
    public function __construct(string $type, string $href, string $text, string $icon, string $class, array $attr = [])
    {
        if (!in_array($type, ['a', 'button'])) {
            throw new \InvalidArgumentException('Only "a" or "button" allowed as type');
        }

        $this->type  = $type;
        $this->href  = $href;
        $this->text  = $text;
        $this->icon  = $icon;
        $this->class = $class;
        $this->attr  = ['class' => $class] + $attr;

        if ('a' !== $type) {
            $this->attr['data-href'] = $href;
        } else {
            $this->attr['href'] = $href;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $attr = [];
        foreach ($this->attr as $name => $value) {
            if (empty(trim($value))) {
                continue;
            }

            if ($value === true) {
                $attr[] = " {$name}=\"{$name}\"";
            } else {
                $value  = addcslashes($value, '"');
                $attr[] = " {$name}=\"{$value}\"";
            }
        }

        return sprintf(
            '<%s %s>%s</%s>',
            $this->type,
            trim(implode($attr)),
            trim(
                sprintf(
                    '%s %s',
                    !empty($this->icon) ? sprintf('<i class="%s"></i>', $this->icon) : '',
                    $this->text
                )
            ),
            $this->type
        );
    }
}