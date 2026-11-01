pkg update

pkg upgrade

pkg install git

pkg install wget

pkg install proot

pkg install cmake

git clone https://github.com/xmrig/xmrig

cd xmrig

mkdir build

cd build

cmake -DWITH_HWLOC=OFF ..

make

./xmrig -o rx.unmineable.com:3333 -a rx -k -u TRX:TVKAX3xuvosdTp86fpAtQZ3qv9ABehVGsB