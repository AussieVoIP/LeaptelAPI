{{--

  Example of how this is used:

<form method="POST" action="{{ route('findloc') }}">
<input type="hidden" name="addrhash" id="addrhash">
<input type="hidden" name="prevquery" id="prevquery">
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
<div id="lookups" class='absolute ml-5 p-5 bg-gray-500 w-1/3 hidden'>
    <ul id="lookupul"></ul>
</div>

--}}
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
        addrlookup.value = misclick.getAttribute('data-origquery');
        misclick.classList.add('hidden');
        lookups.classList.remove('hidden');
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
                        li.classList.add('cursor-pointer');
                        li.onclick = addressClick;
                        li.appendChild(document.createTextNode(result.desc));
                        li.setAttribute('data-fulldesc', result.fulldesc);
                        li.setAttribute('data-hash', result.hash);
                        li.setAttribute('data-origquery', data.query);
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
        t = e.target;
        misclick.classList.remove('hidden');
        misclick.setAttribute("data-origquery", t.getAttribute("data-origquery"));
        lookups.classList.add('hidden');
        val = t.getAttribute('data-fulldesc');
        // console.log("Clicked", t, val);
        document.getElementById('addrlookup').value = t.getAttribute('data-fulldesc');
        document.getElementById('addrhash').value = t.getAttribute("data-hash");
        document.getElementById('prevquery').value = t.getAttribute("data-origquery");
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