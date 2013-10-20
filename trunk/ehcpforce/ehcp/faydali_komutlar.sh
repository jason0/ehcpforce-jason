Bazı faydalı komutlar, 

bir tema dosyasını tüm temalara kopyalama:
cd templates
find ./ -name defaultindexfordomains_en.html -exec cp -v ./sky/en/defaultindexfordomains_en.html {} \;


./test.sh : direk test eder, localde

./repack_ehcp.sh : paketler ve sunucuya yükler (deb paketi degil)
