<x-site::layout title-page="Contact">
    <div class="text-center mb-10">
        <h2 class="text-4xl mb-8">Contact Me</h2>

        <p>
            Please contact me if you have any inquiry or you want to work with
            me. If is something related with a project you have in mind, please
            put as much information you need.
        </p>
    </div>

    <form method="POST" class="grid md:grid-cols-2 space-y-4">
        <div class="col-span-full grid grid-cols-subgrid space-y-4 md:space-y-0 md:space-x-4">
            <x-site::input id="name" name="name" type="text" placeholder="name" class="col-span-1"/>

            <x-site::input id="lastName" type="text" name="lastName" placeholder="last name" class="col-span-1"/>
        </div>

        <div class="col-span-full">
            <x-site::textarea id="message" type="text" name="message" placeholder="your message" rows="5"/>
        </div>

        <div class="col-span-full flex justify-end">
            <button class="px-6 py-2 bg-[rgba(51,255,51,1.0)] cursor-pointer">
                SUBMIT
            </button>
        </div>
    </form>
</x-site::layout>
