<?php
/**
 * Created by Michaël Perrin
 *
 * Source: http://www.michaelperrin.fr/2013/03/19/range-date-validator-for-symfony2/
 *
 * Modificaciones por Luis Ramón López López
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateRangeValidator extends ConstraintValidator
{

    /**
     * @param string $message Violation message
     * @param array $parameters Message parameters
     */
    protected function addViolation($message, $parameters)
    {
        if ($this->context instanceof Symfony\Component\Validator\Context\ExecutionContext) {
            $context = $this->context->buildViolation($message);

            foreach ($parameters as $name => $value) {
                $context = $context->setParameter($name, $value);
            };

            $context->addViolation();
        }
        else {
            throw new LogicException('Context API not 2.5 compatible');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        if (!($value instanceof \DateTime)) {
            $this->addViolation($constraint->invalidMessage,
                ['{{ value }}' => $value]);

            return;
        }

        if (null !== $constraint->max && $value > $constraint->max) {
            $this->addViolation($constraint->maxMessage, [
                '{{ value }}' => $this->formatDate($value),
                '{{ limit }}' => $this->formatDate($constraint->max)
            ]);
        }

        if (null !== $constraint->min && $value < $constraint->min) {
            $this->addViolation($constraint->minMessage, [
                '{{ value }}' => $this->formatDate($value),
                '{{ limit }}' => $this->formatDate($constraint->min)
            ]);
        }
    }

    protected function formatDate($date)
    {
        $formatter = new \IntlDateFormatter(
            null,
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::NONE,
            date_default_timezone_get(),
            \IntlDateFormatter::GREGORIAN
        );

        return $this->processDate($formatter, $date);
    }

    /**
     * @param  \IntlDateFormatter $formatter
     * @param  \Datetime          $date
     * @return string
     */
    protected function processDate(\IntlDateFormatter $formatter, \Datetime $date)
    {
        return $formatter->format((int) $date->format('U'));
    }
}
