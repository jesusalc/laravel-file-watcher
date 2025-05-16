#!/usr/bin/env bash
# set -exu
pathpat="(/[^/]*)+:[0-9]+"
ccred=$(echo -e "\033[0;31m")
ccyellow=$(echo -e "\033[0;33m")
ccend=$(echo -e "\033[0m")
# sed -E
# -e "/[Ee]rror[: ]/ s%$pathpat%$ccred&$ccend%g"
# -e "/[Ww]arning[: ]/ s%$pathpat%$ccyellow&$ccend%g"

nodemon --watch "./" --ext py,go,bash,sh,kv,php,rb,java --spawn --exec "[[ -n \"$(ps aux | grep './tests.bash' | grep -v 'grep'  | head -1 | xargs)\"  ]] || kill \"$(ps aux | grep './tests.bash' | grep -v 'grep'  | head -1 | xargs | cut -d' ' -f2 )\" || bash \"/home/zeus/_/applctns/2025/localbrandx/laravel-file-watcher/./tests.bash\" && bash /home/zeus/_/applctns/2025/localbrandx/laravel-file-watcher/./tests.bash  ./tests.bash"  2>&1            | sed --unbuffered '/\ process\ jobs\ in\ this\ environment/d' \
        | sed --unbuffered '/Top\ level\ /d' \
        | sed --unbuffered '/DEPRECATION WARNING..desired_principal_rate_change_amount=/d' \
        | sed --unbuffered '/app.commands.loan.applications.create.rb.../d' \
        | sed --unbuffered '/block....levels/d' \
        | sed --unbuffered '/\ warning:\ already\ initialized\ constant/d' \
        | sed --unbuffered '/\ was\ here/d' \
      | sed --unbuffered 's/\ on\ line\ /:/g' \
      | sed --unbuffered 's/:\ line\ /:/g' \
      | sed --unbuffered 's/\ No\ such\ file\ or\ directory\ in\ /\ in\ \o033[38;5;213m/g' \
      | sed --unbuffered 's/\,\ no\ offenses\ /\,\ \o033[01;32m no\ offenses\ \o033[0m/g' \
      | sed --unbuffered 's/\ examples\,\ 0\ failures/\o033[01;32m\ examples\,\ 0\ failures \o033[0m/g' \
      | sed --unbuffered 's/\ returns\ /\o033[01;32m\ returns\ /g' \
      | sed --unbuffered 's/\ Do\ not\ use\ /\o033[48;5;235m\o033[38;5;196m\ Do\ not\ use\ /g' \
      | sed --unbuffered 's/..Failure.Error:/\o033[48;5;235m\o033[38;5;196m..Failure.Error:/g' \
      | sed --unbuffered 's/.FAILED\ /\o033[48;5;235m\o033[38;5;196m...FAILED\ \o033[0m/g' \
      | sed --unbuffered 's/.failures:/\o033[48;5;235m\o033[38;5;196m.failures\o033[0m:/g' \
      | sed --unbuffered 's/\ in\ tests\./\ in\ tests\.\o033[0m/g' \
      | sed --unbuffered 's/expected\ the\ /\o033[0mexpected\ the\ /g' \
      | sed --unbuffered 's/\[Command\ was\ successful\]/\o033[01;32m\[Command\ was\ successful\]/g' \
      | sed --unbuffered 's/\ GET\ /\o033[01;35m\ GET\ /g' \
      | sed --unbuffered 's/\ POST\ /\o033[01;35m\ POST\ /g' \
      | sed --unbuffered 's/\ DELETE\ /\o033[01;35m\ DELETE\ /g' \
      | sed --unbuffered 's/\ PUT\ /\o033[01;35m\ PUT\ /g' \
      | sed --unbuffered 's/\ PATCH\ /\o033[01;35m\ PATCH\ /g' \
      | sed --unbuffered 's/\ OPTIONS\ /\o033[01;35m\ OPTIONS\ /g' \
      | sed --unbuffered 's/Top\ /\o033[38;5;100mTop\ /g' \
      | sed --unbuffered 's/\ seconds\ /\o033[38;5;231m\ seconds\ /g' \
      | sed --unbuffered 's/\ behaves\ like\ /\o033[01;36m\ behaves\ like\ /g' \
      | sed --unbuffered 's/\ when\ /\o033[01;36m\ when\ /g' \
      | sed --unbuffered 's/\ do\ /\o033[0m\ do\ /g' \
      | sed --unbuffered 's/\ does\ /\o033[0m\ does\ /g' \
      | sed --unbuffered 's/\ finds\ /\o033[0m\ finds\ /g' \
      | sed --unbuffered 's/\ serializes\ /\o033[0m\ serializes\ /g' \
      | sed --unbuffered 's/ErrorException:\ /\o033[38;5;196mErrorException:\ \o033[38;5;213m/g' \
       | sed --unbuffered "s/\ require_once(.\/wp-content/\ require_once(\n\/home\/zeus\/_\/applctns\/2025\/localbrandx\/laravel-file-watcher\/wp-content/g" \
       | sed --unbuffered "s/\ require(.\/wp-content/\ require(\n\/home\/zeus\/_\/applctns\/2025\/localbrandx\/laravel-file-watcher\/wp-content/g" \
       | sed --unbuffered "s/\ require\ .\/wp-content/\ require\ \n\/home\/zeus\/_\/applctns\/2025\/localbrandx\/laravel-file-watcher\/wp-content/g" \
       | sed --unbuffered "s/\ include\ .\/wp-content/\ include\ \n\/home\/zeus\/_\/applctns\/2025\/localbrandx\/laravel-file-watcher\/wp-content/g" \
       | sed --unbuffered "s/\ include_once(.\/wp-content/\ include_once(\n\/home\/zeus\/_\/applctns\/2025\/localbrandx\/laravel-file-watcher\/wp-content/g" \
       | sed --unbuffered "s/\ in\ \//\ in\ \n\//g" \
      | sed --unbuffered 's/Stack\ trace:/\o033[38;0mStack\ trace:/g' \
      | sed --unbuffered 's/Call\ Stack:/\o033[38;0mCall\ Stack:/g' \
      | sed --unbuffered 's/#. //g' \
      | sed --unbuffered -E "/[Ee]rror[: ]/ s%$pathpat%$ccred&$ccend%g" \
      | sed --unbuffered -E "/[Ww]arning[: ]/ s%$pathpat%$ccyellow&$ccend%g" \
      | sed --unbuffered 's/\.\/spec\//\o033[0m\.\/spec\//g' \

