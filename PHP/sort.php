<?php
/**
Tên hàm					Sắp xếp theo		Thay đổi thứ tự khóa			Loại sắp xếp
array_multisort()		value				kết hợp thì có, số thì không	tùy chọn
asort()					value				có								thấp đến cao
arsort()				value				có								cao đến thấp
ksort()					key					có								thấp đến cao
krsort()				key					có								cao đến thấp
natcasesort()			value				có								tự nhiên
natsort()				value				có								tự nhiên
rsort()					value				không							cao đến thấp
shuffle()				value				không							ngẫu nhiên
sort()					vfprintf(handle, format, args)alue	không			thấp đến cao
uasort()				value				có								user tự định nghĩa
uksort()				key					có								user tự định nghĩa
usort()					value				không							user tự định nghĩa
*/


usort($giftList, function ($item1, $item2) {
	if ($item1['price'] == $item2['price']) return 0;
	return $item1['price'] < $item2['price'] ? -1 : 1;
});
