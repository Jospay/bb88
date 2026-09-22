<script setup>
import { Head } from "@inertiajs/vue3";
import logoIcon from "@/assets/logo.png";

const refundEmail = "info@eswfederation.org";

const refundEmailSubject = "Registration Fee Refund Request";

const refundEmailBody = `Name of Participant / Team:

Contact Number:

Email Address:

Registration Details:

Proof of Payment:

Payment Reference / Transaction Number:

Preferred payment account / details for return:
`;

// Open Gmail compose with the recipient, subject, and blank fields pre-filled.
const refundGmail = `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(
    refundEmail,
)}&su=${encodeURIComponent(
    refundEmailSubject,
)}&body=${encodeURIComponent(refundEmailBody)}`;
</script>

<template>
    <Head>
        <link rel="icon" type="image/png" :href="logoIcon" />
    </Head>

    <div
        class="bg-[url('@/assets/bg.jpg')] bg-cover bg-fixed bg-center py-12 bg-no-repeat min-h-screen w-full grid place-items-center"
    >
        <!-- Top Bar / Partner Logo -->
        <div class="mx-auto w-full max-w-[1320px] px-5">
            <div class="flex flex-col md:flex-row justify-end">
                <div
                    class="flex gap-5 flex-col sm:flex-row items-center justify-between"
                >
                    <p class="text-white text-lg">In Cooperation with:</p>

                    <img
                        src="@/assets/landing-bb88-logo.png"
                        class="sm:h-[50px] h-auto sm:w-auto w-full"
                        alt="Partner Logo"
                    />
                </div>
            </div>

            <div class="text-[#DCDBE0] flex gap-5 mt-7 sm:text-lg text-md">
                <!-- <a href="/register">BACK</a> -->
            </div>
        </div>

        <!-- Title Band -->
        <div class="mt-4 pb-3 relative mx-auto w-full max-w-[1500px] px-5">
            <div
                class="absolute inset-0 opacity-80"
                style="
                    background: linear-gradient(
                        to right,
                        rgba(20, 124, 195, 0),
                        #147cc3 25%,
                        #bf38a6 75%,
                        rgba(191, 56, 166, 0)
                    );
                "
            ></div>

            <div class="mx-auto w-full max-w-[1320px] pt-2">
                <h1
                    class="font-gaming text-center md:pt-0 pt-2 text-white md:text-5xl text-3xl relative z-10"
                >
                    <i>OFFICIAL NOTICE OF EVENT CANCELLATION</i>
                </h1>

                <p
                    class="text-center text-white relative z-10 mt-2 sm:text-lg text-md"
                >
                    National E-Sports MOBA Championship &ndash; &ldquo;Barangay
                    Ko vs. Barangay Mo&rdquo;
                </p>

                <p
                    class="text-center text-white relative z-10 sm:text-lg text-md"
                >
                    September 25 &ndash; 27, 2026
                </p>
            </div>
        </div>

        <!-- Body Content -->
        <div
            class="mt-12 relative mx-auto w-full max-w-[1500px] pt-8 pb-8 sm:px-10"
        >
            <div
                class="opacity-80 absolute inset-0 z-0"
                style="
                    background: linear-gradient(
                        to right,
                        rgba(20, 124, 195, 0),
                        #147cc3 25%,
                        #bf38a6 75%,
                        rgba(191, 56, 166, 0)
                    );
                "
            ></div>

            <div
                class="mx-auto w-full max-w-[1320px] px-5 relative z-10 text-white leading-relaxed"
            >
                <div class="border-2 border-white">
                    <!-- Notice Details -->
                    <div class="p-4">
                        <p class="my-3">
                            Dear Participants, Gamers, Teams, Coaches, Parents,
                            and Supporters,
                        </p>

                        <p class="mb-3">
                            On behalf of the ESports World Federation (ESWF) and
                            the organizing committee of the National E-Sports
                            MOBA Championship &ndash; &ldquo;Barangay Ko vs.
                            Barangay Mo,&rdquo; we sincerely regret to inform
                            you that the event originally scheduled for
                            September 25&ndash;27, 2026 has been cancelled.
                        </p>

                        <p class="mb-3">
                            We understand the time, preparation, excitement, and
                            resources that every participating team and gamer
                            has invested in preparing for this event. This
                            decision was therefore not made lightly.
                        </p>

                        <p>
                            The cancellation comes in consideration of the
                            recent massive typhoons and their continuing effects
                            on communities, as well as the broader concerns
                            surrounding recent crises, public safety issues, and
                            reported incidents involving gun violence and
                            schools. In this environment, we believe it is
                            appropriate for the ESWF to take a more careful and
                            responsible approach to the type of gaming
                            activities and entertainment programs we promote at
                            this time.
                        </p>
                    </div>

                    <div
                        class="w-full bg-slate-100 opacity-50 h-px mt-8 mb-6"
                    ></div>

                    <!-- Commitment Section -->
                    <div class="p-4 mt-4">
                        <h2
                            class="font-gaming text-center sm:text-2xl text-xl mb-3"
                        >
                            <i>OUR COMMITMENT TO GAMERS CONTINUES</i>
                        </h2>

                        <p class="mb-3">
                            We want to make it clear that ESWF remains fully
                            committed to the gaming community and to the
                            development of e-sports in the Philippines.
                        </p>

                        <p class="mb-3">
                            This cancellation does not mean that we are
                            abandoning competitive gaming.
                        </p>

                        <p class="mb-3">
                            Instead, we will temporarily place greater emphasis
                            on entertainment, educational, creative,
                            community-building, and skills-development games and
                            activities, while taking a more measured approach
                            toward combat-oriented and highly aggressive game
                            genres.
                        </p>

                        <p>
                            We will slow down&mdash;not completely
                            remove&mdash;combat-oriented games from our
                            programming. We recognize that these games are part
                            of the broader gaming industry and have a legitimate
                            place within e-sports and entertainment. However, we
                            believe that responsible programming, appropriate
                            safeguards, and consideration of the current social
                            environment are necessary.
                        </p>
                    </div>

                    <div
                        class="w-full bg-slate-100 opacity-50 h-px mt-8 mb-6"
                    ></div>

                    <!-- Culture Goals Section -->
                    <div class="p-4 mt-4">
                        <p class="mb-3">
                            Our objective is to continue building a gaming
                            culture that promotes:
                        </p>

                        <ul
                            class="grid sm:grid-cols-2 grid-cols-1 gap-2 list-disc list-inside"
                        >
                            <li>Sportsmanship and fair play</li>
                            <li>Education and learning</li>
                            <li>Creativity and innovation</li>
                            <li>Teamwork and friendship</li>
                            <li>Digital responsibility</li>
                            <li>Community engagement</li>
                            <li>Healthy entertainment</li>
                            <li>Positive opportunities for young gamers</li>
                        </ul>
                    </div>

                    <div
                        class="w-full bg-slate-100 opacity-50 h-px mt-8 mb-6"
                    ></div>

                    <!-- Refund Section -->
                    <div class="p-4 mt-4">
                        <h2
                            class="font-gaming text-center sm:text-2xl text-xl mb-3"
                        >
                            <i>FOR PARTICIPANTS WHO HAVE ALREADY PAID</i>
                        </h2>

                        <p class="mb-3">
                            For all participants, teams, or representatives who
                            have already paid their registration fees, please
                            send an email to:

                            <a
                                :href="refundGmail"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="font-bold underline hover:no-underline cursor-pointer"
                            >
                                info@eswfederation.org
                            </a>
                        </p>

                        <p class="mb-2">Please include:</p>

                        <ol class="list-decimal list-inside grid gap-1 mb-3">
                            <li>Name of Participant / Team</li>
                            <li>Contact Number</li>
                            <li>Email Address</li>
                            <li>Registration Details</li>
                            <li>Proof of Payment</li>
                            <li>
                                Payment Reference / Transaction Number, if
                                available
                            </li>
                            <li>
                                Preferred payment account/details for the return
                                of the registration fee
                            </li>
                        </ol>

                        <p class="mb-3">
                            Our team will verify each payment and coordinate the
                            appropriate return of the corresponding registration
                            fee.
                        </p>

                        <p>
                            We sincerely ask for your patience while our team
                            processes and verifies all submitted payment
                            records.
                        </p>
                    </div>

                    <div
                        class="w-full bg-slate-100 opacity-50 h-px mt-8 mb-6"
                    ></div>

                    <!-- Apology Section -->
                    <div class="p-4 mt-4">
                        <h2
                            class="font-gaming text-center sm:text-2xl text-xl mb-3"
                        >
                            <i>OUR SINCERE APOLOGY</i>
                        </h2>

                        <p class="mb-3">
                            To everyone who registered, prepared their teams,
                            promoted the event, created content, supported the
                            tournament, and looked forward to competing&mdash;we
                            are truly sorry.
                        </p>

                        <p class="mb-3">
                            We know that cancelling an event after participants
                            have already prepared can be disappointing and
                            frustrating. We sincerely apologize for the
                            inconvenience and disappointment this decision may
                            cause.
                        </p>

                        <p class="mb-3">
                            At the same time, we believe that responsible event
                            organizers must sometimes make difficult decisions
                            when circumstances change.
                        </p>

                        <p class="text-center italic mb-3">
                            The game may be paused, but the community continues.
                        </p>

                        <p class="mb-3">
                            ESWF will continue working toward a future where
                            e-sports can provide safe, exciting, educational,
                            creative, and meaningful opportunities for gamers
                            across the Philippines.
                        </p>

                        <p>
                            Thank you for your understanding, patience, support,
                            and continued trust in the ESports World Federation.
                        </p>
                    </div>

                    <div
                        class="w-full bg-slate-100 opacity-50 h-px mt-8 mb-6"
                    ></div>

                    <!-- Closing Sign-off -->
                    <div class="text-center mt-8">
                        <p class="mb-1">
                            We look forward to seeing you again in our future
                            programs.
                        </p>

                        <p class="mb-4">
                            With sincere appreciation and regret,
                        </p>

                        <p class="font-gaming sm:text-xl text-lg mb-1">
                            <i>ESPORTS WORLD FEDERATION (ESWF)</i>
                        </p>

                        <p class="mb-1">
                            National E-Sports MOBA Championship Organizing
                            Committee
                        </p>

                        <p class="italic">
                            &ldquo;Building a Responsible, Creative and
                            Inclusive Gaming Community.&rdquo;
                        </p>
                    </div>

                    <div
                        class="w-full bg-slate-100 opacity-50 h-px mt-8 mb-6"
                    ></div>

                    <!-- Reference Footer -->
                    <div class="p-4 text-center">
                        <h2 class="font-gaming sm:text-2xl text-xl mb-3">
                            <i>EVENT REFERENCE</i>
                        </h2>

                        <p class="mb-1">NATIONAL E-SPORTS MOBA CHAMPIONSHIP</p>

                        <p class="mb-1">BARANGAY KO VS. BARANGAY MO</p>

                        <p class="mb-1">
                            Originally scheduled: September 25&ndash;27, 2026
                        </p>

                        <p class="mb-1">
                            Registration Website:
                            <a
                                href="https://mobaebz.bb88advertising.com"
                                class="text-brand-green font-bold hover:underline"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                mobaebz.bb88advertising.com
                            </a>
                        </p>

                        <p>
                            Refund/Registration Concerns:
                            <a
                                :href="refundGmail"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-brand-green font-bold hover:underline"
                            >
                                info@eswfederation.org
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@font-face {
    font-family: "GamingSporty";
    src: url("../../../fonts/GamingSporty.ttf") format("truetype");
}

.font-gaming {
    font-family: "GamingSporty", sans-serif;
}
</style>
