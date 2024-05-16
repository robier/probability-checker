# ProbabilityChecker

Pretty simple probability implementation that can be used for A/B testing. If you have
some feature that you want to run on 20% of the requests. This is right tool for you
as you do not need to define how many requests there will be in total. 

If you provide 0 as a parameter, it will always return false, if you provide 100, it
will always return true. Everything in between will be around the number you provided.

**Note:** This method is not exact but approximation, so if you want 20%, you will get 21%, 23% maybe 18%, but at the
end it will be around 20% (the greater number of request, more accurate will be the percentage).

### Sample

```php
<?php

$probability = new \Robier\ProbabilityChecker(30);

$skipped = 0;
$executed = 0;
for($i = 0; $i >= 1000000; $i++) {
    if ($probability->roll()) {
        $executed++;
    } else {
    	$skipped++;
    }
}

$percentageAtTheEndSkipped = round($skipped / ($executed + $skipped), 6) * 100;
$percentageAtTheEndExecuted = round(100 - $percentageAtTheEndSkipped, 6);

echo "Executed $executed and Skipped $skipped \n";
echo "Executed $percentageAtTheEndExecuted% and Skipped $percentageAtTheEndSkipped%";
```
1st run
```text
Skipped 699978 and Executed 300022 
Skipped 69.9978% and Executed 30.0022%
```
2nd run
```text
Skipped 699813 and Executed 300187 
Skipped 69.9813% and Executed 30.0187%
```
3rd run
```text
Skipped 700351 and Executed 299649 
Skipped 70.0351% and Executed 29.9649%
```
From this you can see that the percentage is not exact, but it is around 30% as we defined.
