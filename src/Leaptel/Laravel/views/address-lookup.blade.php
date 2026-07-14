{{--

  Example of how this is used:

<form method="POST" action="{{ route('findloc') }}">
<input type="hidden" name="addrhash" id="addrhash">
<input type="hidden" name="prevquery" id="prevquery">
<input type="hidden" name="querysource" id="querysource">
@csrf
<div class='p-1'>
    <x-input-label for="addrlookup" :value="__('Find Location ID from Address')" />
    <x-text-input id="addrlookup" name="addrlookup" placeholder="1 Smith St, Sydney, NSW, 2000" />
    <x-primary-button class="xms-3">
        {{ __('Search for Address') }}
    </x-primary-button>
    &nbsp;
    <x-secondary-button id="misclick" class="xms-3 hidden">
        {{ __('Undo Mis-click') }}
    </x-secondary-button>
</div>
</form>
<div id="lookups" class='absolute ml-5 p-5 bg-gray-500 w-1/2 hidden'>
    <ul></ul>
</div>

// You can also add a standard text input called 'locid', which will also get filled in
// when you select an item in the dropdown, instead of having the POST form. Example:

<form method="GET" action="{{ route('servicequal') }}">
    <div class='p-1'>
        <x-input-label for="locid" :value="__('Run Service Qualification for Location ID')" />
        <x-text-input id="locid" name="locid" placeholder="LOC...." :value="old('LOC....')" required autofocus />
        <x-input-error :messages="$errors->get('locid')" class="mt-2" />
        <x-primary-button class="xms-3">
            {{ __('Qualify') }}
        </x-primary-button>
    </div>
</form>

// You need to add @include("leaptel::address-lookup") to pull this code in, probably
// in your main app theme or something that's on every page.

--}}

<style>
    .tooltip {
        visibility: hidden;
        position: absolute;
    }

    .has-tooltip:hover .tooltip {
        visibility: visible;
        z-index: 100;
        outline-color: #6b7280;
        outline-width: 1px;
        outline-style: solid;
        margin-left: 3em;
        margin-top: -2em;
    }
</style>

<script>
    function startAddressAutocomplete() {
        const i = document.getElementById('addrlookup');
        const misclick = document.getElementById('misclick');
        if (i) {
            i.addEventListener('input', processAutocomplete);
        }
        if (misclick) {
            misclick.onclick = revertInput;
        }
    }

    function revertInput() {
        const addrlookup = document.getElementById('addrlookup');
        const misclick = document.getElementById('misclick');
        const lookups = document.getElementById('lookups');
        const locid = document.getElementById('locid');
        addrlookup.value = misclick.getAttribute('data-origquery');
        misclick.classList.add('hidden');
        lookups.classList.remove('hidden');
        // This may not be present
        if (locid) {
            locid.value = "";
        }
    }


    function processAutocomplete(x) {
        const lookups = document.getElementById('lookups');
        const misclick = document.getElementById('misclick');
        v = x.target.value;
        if (v.length < 5) {
            lookups.classList.add('hidden');
            misclick.classList.add('hidden');
            return;
        }
        p = new URLSearchParams({
            q: v
        });
        url = "{{ route('address') }}?" + p.toString();
        // console.log("I want to go to " + url);
        fetch(url, {
            credentials: "include"
        }).
        then(status).
        then(json).
        then(data => {
            // window.qqq = data;
            // console.log(data.query);
            if (data.result) {
                result = data.result;
                lookups.classList.remove('hidden');
                var ul = document.createElement('ul');
                ul.setAttribute("id", "lookupul");

                if (result.length == 0) {
                    var li = document.createElement('li');
                    li.appendChild(document.createTextNode("No results found"));
                    ul.appendChild(li);
                    misclick.classList.add('hidden');
                } else {
                    result.forEach(result => {
                        var li = document.createElement('li');
                        // li.appendChild(document.createTextNode(result.desc));
                        if (!result.display) {
                            li.appendChild(document.createTextNode(result.desc));
                        } else {
                            li.innerHTML = result.display;
                        }
                        if (result.valid) {
                            li.classList.add('cursor-pointer');
                            li.onclick = addressClick;
                            li.setAttribute('data-fulldesc', result.fulldesc);
                            li.setAttribute('data-hash', result.hash);
                            li.setAttribute('data-origquery', data.query);
                            li.setAttribute('data-querysource', data.source);
                        }
                        ul.appendChild(li);
                    });
                }

                lookups.replaceChildren(ul);
                // console.log(ul);
            } else {
                lookups.classList.add('hidden');
            }
        });
    }

    function addressClick(e) {
        const misclick = document.getElementById('misclick');
        const lookups = document.getElementById('lookups');
        misclick.classList.remove('hidden');
        lookups.classList.add('hidden');

        // This allows the user to click on anything inside the li, and the browser
        // will find the closest
        t = e.target.closest("li");
        misclick.setAttribute("data-origquery", t.getAttribute("data-origquery"));
        val = t.getAttribute('data-fulldesc');
        // console.log("Clicked", t, val);

        hash = t.getAttribute("data-hash");
        document.getElementById('addrhash').value = hash;

        document.getElementById('addrlookup').value = t.getAttribute('data-fulldesc');
        document.getElementById('prevquery').value = t.getAttribute("data-origquery");
        document.getElementById('querysource').value = t.getAttribute("data-querysource");
        // If hash starts with LOC, then set the locid input value, if it exists.
        if (hash.startsWith("LOC")) {
            const locid = document.getElementById('locid');
            if (locid) {
                locid.value = hash;
            }
        }
    }

    // Annoying Async handlers
    function status(response) {
        if (response.status >= 200 && response.status < 300) {
            return Promise.resolve(response)
        } else {
            return Promise.reject(new Error(response.statusText))
        }
    }

    function json(response) {
        return response.json()
    }

    // Finally register the autocomplete
    document.addEventListener("DOMContentLoaded", () => {
        startAddressAutocomplete();
    });
</script>