import{_ as c}from"./AppLayout-dfa019e6.js";import p from"./DeleteUserForm-ce16856b.js";import l from"./LogoutOtherBrowserSessionsForm-02aeb374.js";import{S as s}from"./SectionBorder-faeb6619.js";import f from"./TwoFactorAuthenticationForm-55dfd1df.js";import u from"./UpdatePasswordForm-ebb0fce2.js";import _ from"./UpdateProfileInformationForm-3b9cb1a7.js";import{o,c as d,w as n,a as i,e as r,b as t,f as a,F as h}from"./app-973b6018.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./DialogModal-b5ce16da.js";import"./SectionTitle-6dd2a0d8.js";import"./DangerButton-8fafd0cf.js";import"./TextInput-12661edc.js";import"./SecondaryButton-366bddc7.js";import"./ActionMessage-f58de766.js";import"./PrimaryButton-680d5343.js";import"./InputLabel-5105e511.js";import"./FormSection-9520b132.js";const g=i("h2",{class:"font-semibold text-xl text-gray-800 leading-tight"}," Profile ",-1),$={class:"max-w-7xl mx-auto py-10 sm:px-6 lg:px-8"},w={key:0},k={key:1},y={key:2},z={__name:"Show",props:{confirmsTwoFactorAuthentication:Boolean,sessions:Array},setup(m){return(e,x)=>(o(),d(c,{title:"Profile"},{header:n(()=>[g]),default:n(()=>[i("div",null,[i("div",$,[e.$page.props.jetstream.canUpdateProfileInformation?(o(),r("div",w,[t(_,{user:e.$page.props.auth.user},null,8,["user"]),t(s)])):a("",!0),e.$page.props.jetstream.canUpdatePassword?(o(),r("div",k,[t(u,{class:"mt-10 sm:mt-0"}),t(s)])):a("",!0),e.$page.props.jetstream.canManageTwoFactorAuthentication?(o(),r("div",y,[t(f,{"requires-confirmation":m.confirmsTwoFactorAuthentication,class:"mt-10 sm:mt-0"},null,8,["requires-confirmation"]),t(s)])):a("",!0),t(l,{sessions:m.sessions,class:"mt-10 sm:mt-0"},null,8,["sessions"]),e.$page.props.jetstream.hasAccountDeletionFeatures?(o(),r(h,{key:3},[t(s),t(p,{class:"mt-10 sm:mt-0"})],64)):a("",!0)])])]),_:1}))}};export{z as default};